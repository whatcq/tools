package main

import (
	"fmt"
	"log"
	"net/http"
	"sync"
	"time"
)

/*
@author Cqiu
@date 2023-11-30
用golang实现电梯模拟程序：
电梯每1s运行一层，到达目标楼层停顿3s。
http请求：
- / 页面：用websocket显示电梯当前楼层和运行方向，页面可以输入用户当前所在楼层和上下键
- /:n/(up/down) 楼层使用请求
- /:n/go (电梯内)去目标楼层请求
- /:n/cancel-(up/down/go) 取消请求 @todo
- /stop|start 停止|开始
*/

import (
	"github.com/gorilla/websocket"
	"strconv"
	"strings"
)

const (
	UP   = 0b001
	DOWN = 0b010
	GO   = 0b100
)

type Elevator struct {
	minFloor    int     // 最小楼层
	maxFloor    int     // 最大楼层
	floor       int     // 当前楼层
	direction   int     // 运行方向，1是向上,-1是向下，0是停止
	floors      []uint8 // 每个楼层的请求 key是楼层, value是请求，bit表示，0b001向上，0b010向下，0b100去目标楼层
	mu          sync.Mutex
	stopSignal  chan bool
	connections []*websocket.Conn
}

func NewElevator(minFloor, maxFloor int) *Elevator {
	elevator := &Elevator{
		minFloor:    minFloor,
		maxFloor:    maxFloor,
		floor:       minFloor,
		direction:   0,
		floors:      make([]uint8, maxFloor+1),
		stopSignal:  make(chan bool),
		connections: make([]*websocket.Conn, 0),
	}

	for i := minFloor; i <= maxFloor; i++ {
		elevator.floors[i] = 0
	}

	return elevator
}

func (e *Elevator) SetDirection(direction int) {
	e.direction = direction
}

func (e *Elevator) SetFloorRequest(floor int, request uint8) {
	if floor < e.minFloor || floor > e.maxFloor {
		fmt.Println("Invalid floor: " + strconv.Itoa(floor))
		return
	}
	if floor == e.minFloor && request == DOWN {
		fmt.Println("Invalid DOWN floor: " + strconv.Itoa(floor))
		return
	}
	if floor == e.maxFloor && request == UP {
		fmt.Println("Invalid UP floor: " + strconv.Itoa(floor))
		return
	}
	e.mu.Lock()
	defer e.mu.Unlock()
	e.floors[floor] |= request
}

func (e *Elevator) ClearFloorRequest(floor int, request uint8) {
	e.mu.Lock()
	defer e.mu.Unlock()
	e.floors[floor] &= ^request
}

func (e *Elevator) hasFloorRequest(floor int, request uint8) bool {
	e.mu.Lock()
	defer e.mu.Unlock()
	return (e.floors[floor] & request) != 0
}

func (e *Elevator) stopAtFloor(floor int) {
	e.mu.Lock()
	defer e.mu.Unlock()

	fmt.Printf("Elevator has arrived at floor %d\n", floor)

	if len(e.connections) > 0 {
		e.sendElevatorStatus()
	}

	time.Sleep(3 * time.Second)
}

func (e *Elevator) sendElevatorStatus() {
	if len(e.connections) < 1 {
		return
	}
	e.mu.Lock()
	defer e.mu.Unlock()

	for _, conn := range e.connections {
		err := conn.WriteJSON(map[string]interface{}{
			"floor":     e.floor,
			"direction": e.direction,
		})
		if err != nil {
			log.Println("Error sending elevator status:", err)
		}
	}
}
func (e *Elevator) sendElevatorInfo(info string) {
	if len(e.connections) < 1 {
		return
	}
	e.mu.Lock()
	defer e.mu.Unlock()

	for _, conn := range e.connections {
		err := conn.WriteJSON(map[string]interface{}{
			"clear": info,
		})
		if err != nil {
			log.Println("Error sending elevator status:", err)
		}
	}
}

// 电梯开始服务：flow:
// checkRequests(default check_direction=1): set direction
// run floor+direction; check the floor;
func (e *Elevator) startService() {
	for {
		direction := 0

		for floor, request := range e.floors {
			if floor < e.minFloor {
				continue
			}
			// 逆序
			if e.direction == UP {
				floor = e.maxFloor + e.minFloor - floor
				request = e.floors[floor]
			}
			if request != 0 {
				if e.floor > floor {
					direction = -UP
				} else if e.floor < floor {
					direction = UP
				} else if (e.direction == UP || floor == e.minFloor) && request&UP == UP {
					direction = UP
				} else {
					direction = -UP
				}
				println("floor: ", floor, " request: ", request, "===floor: ", e.floor, " direction: ", e.direction)
				break
			}
		}

		if direction == 0 {
			if direction != e.direction {
				println("Direction changed: ", e.direction, " -> ", direction)
				e.SetDirection(direction)
				e.sendElevatorStatus()
			}
			time.Sleep(100 * time.Millisecond)
			continue
		}

		e.SetDirection(direction)
		request := e.floors[e.floor]

		// 送到
		wait := time.Duration(0)
		if request&GO == GO {
			e.floors[e.floor] ^= GO
			println("arrive!")
			e.sendElevatorInfo("go_" + strconv.Itoa(e.floor))
			wait = time.Duration(3)
		}
		// 接到
		if e.hasFloorRequest(e.floor, UP) && e.direction == UP {
			e.ClearFloorRequest(e.floor, UP)
			println("let's go: UP")
			e.sendElevatorInfo("up_" + strconv.Itoa(e.floor))
			wait = time.Duration(3)
		} else if e.hasFloorRequest(e.floor, DOWN) && e.direction == -UP {
			e.ClearFloorRequest(e.floor, DOWN)
			println("let's go: DOWN")
			e.sendElevatorInfo("down_" + strconv.Itoa(e.floor))
			wait = time.Duration(3)
		}
		// 走一层
		if wait == time.Duration(0) {
			println("run --> floor: " + strconv.Itoa(e.floor) + " direction: " + strconv.Itoa(e.direction))
			e.floor += e.direction
			e.sendElevatorStatus()
			wait = time.Duration(1)
		}
		time.Sleep(wait * time.Second)
	}
}

func handleWebSocket(elevator *Elevator, w http.ResponseWriter, r *http.Request) {
	conn, err := websocket.Upgrade(w, r, nil, 1024, 1024)
	if err != nil {
		log.Println("Error upgrading to WebSocket:", err)
		return
	}

	elevator.connections = append(elevator.connections, conn)

	defer func() {
		elevator.mu.Lock()
		defer elevator.mu.Unlock()
		for i, c := range elevator.connections {
			if c == conn {
				elevator.connections = append(elevator.connections[:i], elevator.connections[i+1:]...)
				break
			}
		}
		conn.Close()
	}()

	err = conn.WriteJSON(map[string]interface{}{
		"floor":     elevator.floor,
		"direction": elevator.direction,
	})
	if err != nil {
		log.Println("Error sending elevator status:", err)
	}

	for {
		_, msg, err := conn.ReadMessage()
		if err != nil {
			log.Println("Error reading message from WebSocket:", err)
			break
		}
		println(string(msg))

		parts := strings.Split(string(msg), "/")
		if len(parts) >= 2 {
			floorStr := parts[0]
			directionStr := parts[1]

			floor, err := strconv.Atoi(floorStr)
			if err != nil || floor < elevator.minFloor || floor > elevator.maxFloor {
				log.Println("Invalid floor:", floorStr)
				continue
			}

			switch directionStr {
			case "up":
				elevator.SetFloorRequest(floor, UP)
			case "down":
				elevator.SetFloorRequest(floor, DOWN)
			case "go":
				elevator.SetFloorRequest(floor, GO)
			default:
				log.Println("Invalid direction:", directionStr)
			}
		}
	}
}

func main() {
	elevator := NewElevator(1, 10)

	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		http.ServeFile(w, r, "index.html")
	})

	http.HandleFunc("/ws", func(w http.ResponseWriter, r *http.Request) {
		handleWebSocket(elevator, w, r)
	})

	go elevator.startService()

	port := ":8080"
	fmt.Println("Server is listening on port", port)
	log.Fatal(http.ListenAndServe(port, nil))
}
