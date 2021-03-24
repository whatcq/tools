const fs = require('fs');
const md5 = require('md5');
let preveMd5 = null, fsWait = false
const filePath = './'
console.log(`正在监听 ${filePath}`);
fs.watch(filePath, (event, filename) => {
    if (filename) {
        if (fsWait) return;
        fsWait = setTimeout(() => {
            fsWait = false;
        }, 100)
        // var currentMd5 = md5(fs.readFileSync(filePath + filename))
        // if (currentMd5 == preveMd5) {
        //     return
        // }
        // preveMd5 = currentMd5
        console.log(`${event} ${filename}`)
    }
})
