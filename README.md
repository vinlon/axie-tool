# AXIE-TOOL

## 安装准备

PHP8.1

安装扩展:
fileinfo gmp redis

去掉禁用函数:
proc_open 

Nginx配置
```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

初始化命令
```shell

php artisan erc1155_token:sync
php scripts/load_body_part.php
```

定时任务

```shell
sudo -H -u www bash -c 'cd /www/wwwroot/axie-tool/ && /www/server/php/81/bin/php artisan schedule:run'
```

Horizon

```shell
[program:axie-tool-horizon]
process_name=%(program_name)s
command=/www/server/php/81/bin/php /www/wwwroot/axie-tool/artisan horizon
autostart=true
autorestart=true
user=forge
redirect_stderr=true
stdout_logfile=/www/wwwroot/axie-tool/storage/logs/horizon.log
stopwaitsecs=3600
```

## TODO
- [x] 排行榜队伍类型统计
- [x] 排行榜在线状态展示，各队伍在线人数展示
- [x] 查看相似axie的在售价，历史价格
- [ ] 用户页面对战记录快速筛选
- [x] 最新繁殖数据监控
