## 一个简单的表单系统, 类似问卷星

2016年11月开工

当时是因为部门活动需求, 需要一个招新表单, 作为技术部的童鞋, 当然更想是通过自己的代码去实现啦\
但又不想做一次性的东西, 想更加通用一些, 所以就有了这个简单的表单系统了

同时也算是入门PHP练手作品, 使用原生PHP, JS, 对事物的封装级别是简约版\
也因为当时入门时候学习资料陈旧, 使用了`mysql_xxx`有sql注入问题的数据API, 所以下线了\
但是后面尝试切换为PDO, 但是发现太多东西要改了, 因为API封装不好的问题, 返回的数据结构和mysql_xxx, 关联起来了\
卒放弃了, 教训就是教程看官网就好了, 后面有时间了也专门过了一遍PHP手册, 写了篇[PHP手册拾遗](https://www.jianshu.com/p/25b8bbb0a613)

#### 特性:

0. 表单编辑
1. 结果下载csv文件
2. 自动保存表单

<div>
  <img width="250" src="https://upload-images.jianshu.io/upload_images/252050-66a52eb3d11f133b.jpg?imageMogr2/auto-orient/strip%7CimageView2/2/w/700">
  <img width="250" src="https://upload-images.jianshu.io/upload_images/252050-382f70966a8512f0.jpg?imageMogr2/auto-orient/strip%7CimageView2/2/w/700">
  <img width="250" src="https://upload-images.jianshu.io/upload_images/252050-f1809b110ece1f1b.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/519">
</div>
