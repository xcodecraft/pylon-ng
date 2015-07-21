#Concept

作为一框架来说,的核心的目的,是在于減少应用项目的开发量,把web领域的通用问题提供一个统一的解决方案。使得开发人员在使用一个
框架后,能真正只用关心和解决应用的业务领域的问题。当然这是一个理想!
从Martin Fowler 的<<企业应用架构模式>>的观点来说,业务越复杂,就越推荐使用领域模型。因此,框架需要支持这种模式,为此框架的很多的组件都是为了 支持这个模型而存在的。(当然,你不用也可以)
- Entity 及模型  业务实体都继承于 Entity 基类。
- 业务实体与永久化逻辑的分析 Dao(数据访问对象)  * 自动更新的机制 unit of work (工作单元)
- 依赖注入
  > 所谓依赖注入,是指程序运行过程中,如果需要调用另一个对象协助时,无须在代码中创建被调用者,而是依赖于
外部的注入。

## 领域模型

领域内的对象模型,组织行为和数据。将领域模型引入到应用中涉及到插入整个的一个对象层在工作的业务区内,这些对象模拟业务数据,捕 捉商业规则。OO领域 模型经常看起来像数据库的模型。尽管他们有许多区别。领域模型混合数据与处理,有多值属性和复杂的网络(图式的)联系,并且可以使用 继承。
基本上有两种风格的领域模型。
简单领域模型:与数据库的设计极为相似大部分情形是一个数据库表对应一个领域对象。 富领域模型:完全不像数据库设计,有继 承,策略和其他的设计模式介入。还有一些小的互连的小对象网格。 富领域模型最好用于复杂的逻辑,但极难影射到数据库。简单领域模型可以使用 活动记录Active Record(相信学习ruby on
rails的爱好者不会不知道此类把,他就是用了这里提到的模式)而复杂的领域(富领域模型)会用到数据影射层(如java的hibernate)。
 因为业务行为经常改变,所以使该层(领域层)方便的修改,构建,和测试是很重要的,最终你会希望领域模型层与系统中的其他层最小化
耦合。层式体系结构模式也都要求尽量与系统的其它层(部分)松耦合。
至于到底使用领域模型的效果如何,这是一个难于回答的主题.因为取决你的系统复杂度(复杂度较翔实的解释参见unix编程艺术--the art of XXX一书).
如果业务规则涉及到许多验证,计算等最好用领域对象处理它们. 如果几乎没有检验和太多的计算TransactionScript事务脚本可 能更适合.
这涉及编程范型转换问题,一个团队或个人当习惯一种思维或设计理念后很难转到其它范型,但一旦适应又很难在转回来.(如面向对象的,面向过程 的,面向方面的,面向组件的,面向契约等等).
即使在变化的情况下也习惯以常规的方法去解决它(惯性吗!)
在使用领域模型时 第一个与数据库的交互时的选择应是数据影射(DataMapper)设计模式,这有助于保持领域模型与数据库间的独立,可有效处理数据库模式与领域 对象的 脱节(比如领域对象中虚属性的引入:DB表中没有相应的列,但对象却有这样的属性[或看起来像是一个属性访问的方法]).
当使用领域模型时可能会考虑引入 服务层ServiceLayer,该层给领域模型一个更精细的API