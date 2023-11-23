## 1 - ls
# 带颜色设置
alias ls='ls --color=auto'
# 长格式输出
alias ll='ls -l --color=auto'
# 显示隐藏文件
alias l.='ls -ld .* --color=auto'
# 长格式显示所有文件，按照时间倒序并显示每个文件的容量
alias lh='ls -alths --color=auto'

## 2 - cd
# 避免日常手误
alias cd..='cd ..'
# 退出当前目录
alias ..='cd ..'
alias ...='cd ../../..'
alias ....='cd ../../../..'
alias .....='cd ../../../..'
alias .2='cd ../..'
alias .3='cd ../../..'
alias .4='cd ../../../..'
alias .5='cd ../../../../..'

## 3 - grep
# 带颜色设置
alias grep='grep --color=auto'
alias egrep='egrep --color=auto'
alias fgrep='fgrep --color=auto'

## 4 - bc
# 使用的标准数学库
alias bc='bc -l'

## 5 - mkdir
# 创建级联目录并打印
alias mkdir='mkdir -pv'

## 6 - diff
#  colordiff 替代 diff 命令，前提：yum install -y colordiff
alias diff='colordiff'

## 7 - date
alias now='date "+%Y-%m-%d %H:%M:%S.%s"'
# 获取秒和毫秒的时间戳，时间戳转换为时间：date "+%Y-%m-%d %H:%M:%S" -d @1619503315
alias timestamp='now; echo s: $(date +"%s"); echo ms: $(echo `expr \`date +%s%N\` / 1000000`)'

## 8 - vim
alias vi=vim
alias svi='sudo vi'

## 9 - 查看端口
alias ports='netstat -tulanp'

## 10 - 危险命令安全设置,
alias mv='mv -i'
alias cp='cp -i'
alias ln='ln -i'
# 不对根目录进行递归操作
alias rm='rm -i --preserve-root'
alias chown='chown --preserve-root'
alias chmod='chmod --preserve-root'
alias chgrp='chgrp --preserve-root'

## 11 - yum update
alias update='yum update'
alias updatey='yum -y update'

## 12 - 磁盘、内存、CPU、进程监控
alias psg='ps -ef | grep '
# 仅显示当前用户的进程
alias psme='ps -ef | grep $USER --color=always '

# 磁盘
alias du1='du -h -d 1'
alias du2='du -h -d 2'
alias du3='du -h -d 3'

# 内存信息
alias meminfo='free -h -l -t'
# cpu信息
alias cpuinfo='lscpu'

# 获取占用内存的进程排名
alias psmem='ps auxf | sort -nr -k 4'
alias psmem10='ps auxf | sort -nr -k 4 | head -10'

# 获取占用 cpu 的进程排名
alias pscpu='ps auxf | sort -nr -k 3'
alias pscpu10='ps auxf | sort -nr -k 3 | head -10'

# 磁盘、内存、端口情况
alias dfn='df -h; free -h -l -t; netstat -tulanp'

## 13 - 短命令
alias h='history'
alias j='jobs -l'

## 14 - git 
alias g='git'
alias gr='git rm -rf'
alias gs='git status'
alias ga='g add'
alias gc='git commit -m'
alias gp='git push origin master'
alias gl='git pull origin master'

## 15 - other
alias ping="time ping"
alias nocomment='grep -Ev "^(#|$)"'
alias tf='tail -f '
