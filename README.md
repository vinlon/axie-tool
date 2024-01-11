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
```

## TODO
- [x] 排行榜队伍类型统计
- [x] 排行榜在线状态展示，各队伍在线人数展示
- [x] 查看相似axie的在售价，历史价格
- [ ] 用户页面对战记录快速筛选
- [x] 最新繁殖数据监控
