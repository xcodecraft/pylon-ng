
# 进程内字典函数
``` php
pylon_dict_data($file,$keyprefix,$valeprefix)
pylon_dict_find($key)
pylon_dict_count()

```

# 进程共享字典
``` php
pylon_sdict_using($name) // 使用某个字典
pylon_sdict_create($name,$size)
pylon_sdict_remove($name)// 清空字典,不是清除某个词,慎用 
pylon_sdict_data($file,$keyprefix,$valeprefix)// 依据文件file,更新字典内容.使用时机:删除某个字典数据后。 
pylon_sdict_find($key)
pylon_sdict_count()
```
