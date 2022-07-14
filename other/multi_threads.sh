#!/bin/bash

mkfifo list
mkfifo lock
exec 3<>list
exec 4<>lock

# 主进程发布任务到队列
# 1 4 5 6x 7 8-

#for i in {"$1".."$2"}; do
for i in {750..799}; do
  echo ${i} >&3
done
# 释放锁
echo >&4

# 开启4个进程
threads=4
for ((i = 0; i < ${threads}; i++)); do
  {
    # 获取锁
    while read -t 1 -u 4 && read -t 1 -u 3 arg; do
      # 释放锁
      echo >&4

      echo "执行任务: ${arg}"
      # 任务代码 start===
      #sleep 3s 注意是双引号！
      curl "https://xxx.com/?sth=${arg}" \
      --compressed \
      --silent -o xxx/$(printf "%04d.ts" ${arg})
      # 任务代码 end===
    done
  } &
done
wait
exec 3>&-
exec 4>&-
rm -rf list
rm -rf lock
