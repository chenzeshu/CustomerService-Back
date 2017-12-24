# 客服平台二期-后端

## simple introduction
1. 采用`laravel`开发
2. 采用`chen-utils`返回数据统一格式(没有使用`Fractal`/5.5的`Eloquent:RESOURCE`)
3. 采用`jwt-auth`进行API验证
4. 基于`redis`实现的低写数据缓存、`实时refresh`(不包括展示, 由于并发较低, 也没有做缓存锁)
5. 基于`redis`实现的sql代价较大的数据缓存化(实测延时减少三分之一, 约100ms)
6. 基于`socket.io`与`laravel-echo-server`实现的在线IM
7. 面相客户端的接口提供基于微信支付向员工打赏的功能
8. 20171216统计表数目 : 47; 预计1.0版表数目: 40+

ps : 注意mysql5.7的sql_mode问题
## Wating to do
1. ~~各个Models的关系设定 + 数据填充seeder文件制作~~(finished)
2. ~~各个restful资源接口的制作~~
3. ~~基于vue的前端制作~~
4. ~~低更新率数据缓存化(由于并发较低, 没有做缓存锁)~~
5. ~~对各个表的索引做了优化, 经测试`500`条以上+`JOIN 5表`以上, sql性能提升一倍.~~
6. ~~将控制器中的一部分模型事件转移到了`Observers`类中~~
6. 自身短信与邮件的队列化
7. 在线IM及CSS美化(自建socket.io)
8. 单元测试
9. 自动化备份
10. 后端表单校验做了财务的正负校验(只做了抛错, 没有做表单验证(表单验证更方便简洁))
11. need to be added

## 项目缺陷
 1. 没有使用缓存锁, 在高并发时有可能产生缓存脏读
    **具体场景**: 登陆时的LoginInfo脏读