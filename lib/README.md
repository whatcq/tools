
### php 框架
提供一套底层功能的基础代码，有了结构和规范。
php 灵活，每个人容易按自己需求和喜好弄一套自己的框架。
写框架=程序设计师，用框架=码农。设计师要考虑功能、性能、灵活性、可扩展性、可维护性等等。
框架的生命周期：需求增加或依赖改变，类分离，臃肿=>卒！然后：新时代，新框架。
架构是权衡的艺术。

我的问题：
- 总是想简单实现，不想增加层次，实际代码就乱
- 总想性能:短小代码(乱)实现基本功能
- 总想兼容旧时代，旧物总觉得有一天可能还用，没有丢弃

#### 我的需求
* 简单使用，几个文件，有基本的结构就行（脚本级框架，不是项目级）：自动加载、默认路由
- 设计：精巧
* micro framework
* autoload：这个不能不用
- container/IoC：至少singleton需要。
* filter/validation
- middleware/event/hook
* cache: adapter/driver/cache-pool
* config
* utils/helper: laravel的Arr/Str不经济，但引入yii就不需要我了！
  - array
  - string
  - file
* debug
  - log
  - debug-bar:error/exception
  - env
  - test(unit/functional/acceptance/fixture)
* db
  - dao/adapter/(db/redis/mongodb/es)/query-builder
  - orm/ar/model/schema/entity/repository
- web
  - request/response
  - security(auth/permission/rate-limit)
  - session/cookie/jwt
  * route: 默认默认，简单，快；
  - restful
  - crud
  - view(url/template/asset/html/widget/form)
  - i18n
  - mail
  - version
- other
  - command
  - queue

### 那些影响我的php framework：
- CI: 2009年入行用这个框架，简洁易懂
- thinkphp: 国产框架，有些自己的东西，还行的
- fleaphp/Qeephp: 川大的自贡哥写的，干净飘逸。
- shortphp：最简单的框架，现在还影响着我
- speedphp: 2010，那时候就喜欢简单干净，作者代码还是很符合我的审美，bowl3开始用thinkphp，后改用speedphp
- doophp: 号称很快，哲学是少用php高级特性魔术方法，代码也干净简洁，没更新多久。
- Yii: 2012才开始用，这才是大佬！Qeephp消退了，前有yii,后有thinkphp
- phalcon: C实现，确实快；但维护不力，特性不更新；另一个yaf我还没试过。
- swoole/workerman: 追求性能必然要看这个
- laravel：2017后开始用，慢、臃肿，不喜欢，但这成了主流，且一直更新，所以重点还在于商业运营，国内就是thinkphp
- owl/Lysine：杨溢，重庆的，川大的，代码更是短小精干(今天再看代码，这就是我想要的)，so good！2012遇到一哥们也写过框架，这就是比我厉害的人。
- slim/lumen/flight/Silex: route模式，我还是觉得默认模式更有性能。


