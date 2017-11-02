# 客服平台二期-后端

## simple introduction
1. 采用`laravel`开发
2. 采用`chen-utils`提供返回数据统一格式(没有使用`Fractal`/5.5的`Eloquent:RESOURCE`)
3. 采用`jwt-auth`进行API验证 
4. 面相客户端的接口提供基于微信支付向员工打赏的功能
5. 20170925统计表数目 : 35; 预计1.0版表数目: 40+

ps : 注意mysql5.7的sql_mode问题
## Wating to do
1. ~~各个Models的关系设定 + 数据填充seeder文件制作~~(finished)
2. 各个restful资源接口的制作
3. 基于vue的前端制作
4. 自身短信与邮件的队列化
5. 单元测试
6. 自动化备份
7. need to be added