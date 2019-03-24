# HideAndSeek

一个使用了Swoole作为服务端，Vue作为前端展示的捉迷藏小游戏

## 运行方法

在app文件夹下运行`php Server.php`即可

## 使用技术
- PHP 7.2 
- Swoole 4.3.0
- Swoole WebSocket Server
- Swoole Task Worker
- Swoole Static Handler
- Vuejs
- HTML WebSocket
- Redis String
- Redis List
- 少量算法逻辑

## 实现了
- 捉迷藏游戏逻辑（逻辑） ★★
- 匹配机制（缓存） ★
- 联机对战（网络编程、缓存） ★★★★
- 游戏结束判定（逻辑） ★

## 可扩展功能（比较懒）
- 当前在线人数：使用Redis Set保存在线人员（缓存） ★
- 多人模式：参与时不限制人数（网络编程） ★★★
- 排行榜：使用Redis SortSet（缓存） ★
- 观战模式：在游戏管理者Game中增加Watcher数组，订阅游戏消息（逻辑） ★★
- 随机地图生成：暂时只想到了多点随机路线拼接（算法） ★★★
- 邀请对战：往被邀请人发一条消息确认开战（网络编程） ★★
- 优化地图展示：手机适配（前端） ★★（这个真不会。。）

## 效果图
![例图1](https://github.com/Zhao-666/HideAndSeek/blob/master/img/example1.png)
![例图2](https://github.com/Zhao-666/HideAndSeek/blob/master/img/example2.png)
