# HideAndSeek

一个使用了Swoole作为服务端，Vue作为前端展示的捉迷藏小游戏

## 效果图
![例图1](https://github.com/Zhao-666/HideAndSeek/blob/master/img/example.gif)
![例图2](https://github.com/Zhao-666/HideAndSeek/blob/master/img/demo2.gif)

## 运行方法

在app文件夹下运行`php Server.php`即可

## 使用工具
- PHPStorm
- VirtualBox
- XShell
- Chrome

## 使用技术
- PHP 7.2 
- Swoole 4.3.0
- Swoole WebSocket Server
- Swoole Task Worker
- Swoole Static Handler
- Swoole Timer After
- Vuejs
- HTML WebSocket
- Redis String
- Redis List
- 少量算法逻辑

## 实现功能
### 捉迷藏游戏逻辑（逻辑） ★★
- 游戏管理类Game
- 玩家类Player
- 地图生成
- 玩家位置移动、边界判断
### 匹配机制（缓存） ★
- 使用WebSocket通信
- 发起Task进行匹配
- 使用Redis List存放匹配玩家
- （可优化点：增加定时器匹配）
### 联机对战（网络编程、缓存） ★★★★
- 根据Code判断客户端和服务端消息类型
- 调用游戏管理类Game创建游戏
- 将游戏数据存放在全局Global变量中
- 每次发起移动都要发送游戏战局数据
### 游戏结束判定（逻辑） ★
- 两个玩家坐标重叠即为游戏结束
### 适配手机浏览器（前端） ★★★
### 监听键盘方向键（前端）★
- 绑定document.onkeydown事件
### 当前在线人数（缓存） ★
- 使用Redis Hash实现
### 玩家排行榜（缓存） ★
- 使用Redis Sorted Set实现
### 邀请玩家对战（网络编程） ★★
- 发送一条消息到对手客户端确认开战
### 游戏时间限制（网络编程） ★★
- 使用swoole_timer_after作为定时器

## 可扩展功能（比较懒）
- 观战模式：在游戏管理者Game中增加Watcher数组，订阅游戏消息（逻辑） ★★
- 断线重连：断线后暂停接收消息（网络编程） ★★★
- 记录游戏战局：结束后发起Task保存数据（数据库） ★★
- 多人模式：参与时不限制人数（网络编程） ★★★
- 随机地图生成：暂时只想到了多点随机路线拼接（算法） ★★★
