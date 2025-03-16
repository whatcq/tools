#!/bin/bash

# 设置服务器参数
if [ "$1" == "dev" ]; then
    SERVER_USER="dev_user"
    SERVER_HOST="1.1.1.217"
    SERVER_PORT="22"
    PROJECT_DIR="/sites/aigc_dev"
    CONTAINER_NAME="hyperf_dev"
elif [ "$1" == "prod" ]; then
    SERVER_USER="root"
    SERVER_HOST="1.1.1.216"
    SERVER_PORT="22"
    PROJECT_DIR="/sites/aigc"
    CONTAINER_NAME="hyperf"
else
    echo "Invalid environment specified. Use 'dev' or 'prod'."
    exit 1
fi

# SSH登录并执行命令
ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST << EOF
    cd $PROJECT_DIR
    git pull origin master

    # 检查composer.json是否有更新
    if git diff --name-only HEAD@{1} HEAD | grep -q 'composer.json'; then
        docker exec $CONTAINER_NAME composer install --no-dev -o
    fi

    # 检查是否有新的迁移文件
    if [ -n "\$(find migrations -type f -newermt "\$(git log -1 --format=%cd --date=iso)" 2>/dev/null)" ]; then
        docker exec $CONTAINER_NAME php bin/hyperf.php migrate
    fi

    # 重启服务
    docker exec $CONTAINER_NAME ./server.sh restart
EOF
