#base
export LANG=zh_CN.utf-8
export TIME_STYLE="+%Y.%m.%d %H:%M:%S"

#PATH=$PATH:/C/Users/Administrator/AppData/Roaming/Composer/vendor/bin
alias paths='echo $PATH|sed "s/:/\n/g"'
pathadd(){
    if [ -n "$1" ] ; then
        new_path="$1"
    else
        new_path=$(pwd)
    fi
    export PATH="$new_path:$PATH"
}

alias ls='ls --show-control-chars --color=auto'
alias ll='ls -lah'

#chdir  
alias ..="cd .."  
alias cdd="cd .."  
alias cd..="cd .."  
alias ...="cd ../.."  
alias ....="cd ../../.."  
alias .....="cd ../../../.."  
alias -- -='cd -'  
  
alias cd.s='echo "`pwd`" > ~/.cdsave'  #cd save : save where i am  
alias cd.b='cd "`cat ~/.cdsave`"'  # cd back

# 简单跳转，like jump,z
to(){
    case $# in
        1) cd $(cat ~/.cd/$1) ;;
        # 跳转到该目录并加入快捷方式：to soft /d/mysoft
        2) cd $2; [ "$1" == "." ] && name=$(basename "$PWD") || name="$1"; echo `pwd -L` > "$HOME/.cd/$name" ;;
        # 显示所有快捷方式； 删除快捷方式需直接删文件
        *) grep --color=auto '/' -r ~/.cd/;; #head ~/.cd/* ;;
    esac
}
wds=$(ls ~/.cd|xargs);complete -W "$wds" to

#git clone --depth 1 https://github.com/junegunn/fzf.git ~/.fzf
#~/.fzf/install
#搜文件：Ctrl+T(=everything)!
[ -f ~/.fzf.bash ] && source ~/.fzf.bash

#git
alias g='git'
alias la='git pull'
alias tui='git push'
alias tui1='git push origin HEAD' #$(git symbolic-ref --short HEAD)
alias gla='git clone --depth=1'
#alias gla2='_a(){ git clone --depth=1 https://ghproxy.com/$1 ; }; _a'
gla2(){
    git clone --depth=1 https://ghproxy.com/$1
}

visit(){
    path=`pwd`
    start "http://localhost:9090/"${path/*\/www\//}
}

#tools
alias h='help_fun(){ $@ --help | less ;};help_fun $1' # eg. h grep
#https://github.com/Russell91/sshrc.git
alias sshrc='~/sshrc/sshrc'

curl_format="\
time_namelookup:    %{time_namelookup}\n\
time_connect:       %{time_connect}\n\
time_appconnect:    %{time_appconnect}\n\
time_pretransfer:   %{time_pretransfer}\n\
time_redirect:      %{time_redirect}\n\
time_starttransfer: %{time_starttransfer}\n\
time_total:         %{time_total}\n\
"
alias curl_timing="curl -so /dev/null -w '$curl_format' "

#program
alias composer='/F/xampp/php/php.exe /G/www/composer.phar'
alias subl='/D/Program\ Files/Sublime\ Text\ 3/sublime_text.exe'
alias vscode='/d/Program\ Files/Microsoft\ VS\ Code/Code.exe'
alias emacs='/D/mysoft/emacs-25.2-x86_64/bin/runemacs.exe'

alias gitk='/d/Program\ Files/Git/cmd/gitk.exe'
alias ie='/c/Program\ Files\ \(x86\)/Internet\ Explorer/iexplore.exe'

alias fanyi='/G/www/7788/fanyi/bin/fanyi'

#workspace
alias vbox='ssh fuer@172.16.1.33'
alias dev='ssh root@101.200.173.1'

alias dp='ssh -l root 101.200.173.1 "cd /data/app/shayijiao && git pull"'
alias 78='cd /d/laragon/www/7788'
alias cqiu='cd /d/laragon/www/cqiu'
alias i='[ -f composer.json ] && composer install || ([ -f package.json ] && npm install)' #安php不一定js

# 自动解压：判断文件后缀名并调用相应解压命令
function q-extract() {
    if [ -f $1 ] ; then
        case $1 in
        *.tar.bz2)   tar -xvjf $1    ;;
        *.tar.gz)    tar -xvzf $1    ;;
        *.tar.xz)    tar -xvJf $1    ;;
        *.bz2)       bunzip2 $1     ;;
        *.rar)       rar x $1       ;;
        *.gz)        gunzip $1      ;;
        *.tar)       tar -xvf $1     ;;
        *.tbz2)      tar -xvjf $1    ;;
        *.tgz)       tar -xvzf $1    ;;
        *.zip)       unzip $1       ;;
        *.Z)         uncompress $1  ;;
        *.7z)        7z x $1        ;;
        *)           echo "don't know how to extract '$1'..." ;;
        esac
    else
        echo "'$1' is not a valid file!"
    fi
}

# 自动压缩：判断后缀名并调用相应压缩程序
function q-compress() {
    if [ -n "$1" ] ; then
        FILE=$1
        case $FILE in
        *.tar) shift && tar -cf $FILE $* ;;
        *.tar.bz2) shift && tar -cjf $FILE $* ;;
        *.tar.xz) shift && tar -cJf $FILE $* ;;
        *.tar.gz) shift && tar -czf $FILE $* ;;
        *.tgz) shift && tar -czf $FILE $* ;;
        *.zip) shift && zip $FILE $* ;;
        *.rar) shift && rar $FILE $* ;;
        esac
    else
        echo "usage: q-compress <foo.tar.gz> ./foo ./bar"
    fi
}

sonar(){
    id=`basename $(pwd)`
    # declare -A map=(["nibs"]="new-internal-biz-system")  
    if [ "$id" = "nibs" ]; then
        id="new-internal-biz-system"
    fi
    echo $id
    # return .13 => .32
    branch=`git rev-parse --abbrev-ref HEAD`
    start "http://172.16.10.32:19000/dashboard?id=${id}&branch=${branch}&resolved=false"
}

exfile(){
    if [ $# -eq 2 ]; then
        file1=$1
        mv $2 $2.bak.cqiu
        mv $1 $2
        mv $2.bak.cqiu $file1
    else
        echo 'Usage: exfile file1 file2\n'
    fi
}

pkm(){
    files="/d/mysoft/mempad64/*.ls? /d/mysoft/ALTRun/*.ls?"
    case $# in
        1) grep -ani --color=auto "$1" $files ;;
        2) grep -ani --color=auto "$1" -A "$2" $files ; echo -e "=====\n"grep -ani --color=auto "$1" -A "$2" $files ;;
        # 显示搜索结果的前后多少行
        3) grep -ani --color=auto "$1" -A "$2" -B "$3" $files ;;
    esac
}

dosh(){
	docker exec -it $1 bash #/usr/bin/sh
}
