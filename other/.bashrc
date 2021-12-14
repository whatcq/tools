#base
export LANG=zh_CN.utf-8
export TIME_STYLE="+%Y.%m.%d %H:%M:%S"

#PATH=$PATH:/C/Users/Administrator/AppData/Roaming/Composer/vendor/bin

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

#git
alias g='git'
alias la='git pull'
alias tui='git push'
alias tui1='git push origin $(git symbolic-ref --short HEAD)'
alias gla='git clone --depth=1'

#tools
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
        exit
    else
        echo 'Usage: exfile file1 file2\n'
    fi
}

pkm(){
	grep -ani --color=auto "$1" /d/mysoft/mempad64/*.ls? /d/mysoft/ALTRun/*.ls?
}
