# 客服平台二期-后端

## simple introduction
1. 采用`laravel`开发
2. 采用`chen-utils`提供返回数据统一格式(没有使用`Fractal`/5.5的`Eloquent:RESOURCE`)
3. 采用`jwt-auth`进行API验证
4. 基于redis实现的低更新率数据缓存、`实时refresh`(不包括展示, 由于并发较低, 也没有做缓存锁)
5. 基于redis实现的重数据缓存化(实测延时减少三分之一, 约100ms)
5. 面相客户端的接口提供基于微信支付向员工打赏的功能
6. 20171206统计表数目 : 40; 预计1.0版表数目: 40+

ps : 注意mysql5.7的sql_mode问题
## Wating to do
1. ~~各个Models的关系设定 + 数据填充seeder文件制作~~(finished)
2. ~~各个restful资源接口的制作~~
3. ~~基于vue的前端制作~~
4. ~~低更新率数据缓存化(不包括展示, 由于并发较低, 也没有做缓存锁)~~
5. 自身短信与邮件的队列化
6. 在线IM及CSS美化(目前不确定使用pusher还是自建socket.io广播)
7. 单元测试
8. 自动化备份
9. need to be added

## 项目缺陷
 1. 没有使用缓存所, 在高并发时有可能产生缓存脏读
    **具体场景**: 登陆时的LoginInfo脏读