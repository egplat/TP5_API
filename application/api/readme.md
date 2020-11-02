### Get Code

> <font color=red>`get`</font>  api.tp5.com/user/time/token/user_contact/is_exist <font color=red>`in-order`</font>

|     参数     |    类型    |          必需/可选          |  默认  |         描述          |
| :----------: | :--------: | :-------------------------: | :----: | :-------------------: |
|   **time**   |  **int**   | <font color=red>必需</font> | **无** |      **时间戳**       |
|  **token**   | **string** | <font color=red>必需</font> | **无** |       **令牌**        |
| user_contact |   string   | <font color=red>必需</font> |   无   | 联系方式（手机/邮箱） |
|   is_exist   |    int     | <font color=red>必需</font> |   无   |    0(不存在)/1(存在)    |

```json
{
    "code":200,
    "msg":"验证码发送成功！",
    "data":"",
}
```

```json
{
    "code":400,
    "msg":"（看返回的具体错误信息）",
    "data":"",
}
```

### Register

> <font color=red>`post`</font>  api.tp5.com/user/register

|     参数     |    类型    |          必需/可选          |  默认  |         描述          |
| :----------: | :--------: | :-------------------------: | :----: | :-------------------: |
|   **time**   |  **int**   | <font color=red>必需</font> | **无** |      **时间戳**       |
|  **token**   | **string** | <font color=red>必需</font> | **无** |       **令牌**        |
|  user_name   |   string   | <font color=red>必需</font> |   无   |    用户名（学号）     |
| user_contact |   string   | <font color=red>必需</font> |   无   | 联系方式（手机/邮箱） |
|   user_pwd   |   string   | <font color=red>必需</font> |   无   |   md5加密的用户密码   |
|     code     |    int     | <font color=red>必需</font> |   无   |        验证码         |

```json
{
    "code":200,
    "msg":"注册成功！",
    "data":{
        "user_name":"*************",//用户名
        "user_contacte":"***********",//用户联系方式
        "user_rtime":"1501212145",//用户注册时间
    },
}
```

```json
{
    "code":400,
    "msg":"（看返回的具体错误信息）",
    "data":"",
}
```

### Login

> <font color=red>`post`</font>  api.tp5.com/user/login

|   参数    |    类型    |          必需/可选          |  默认  |       描述        |
| :-------: | :--------: | :-------------------------: | :----: | :---------------: |
| **time**  |  **int**   | <font color=red>必需</font> | **无** |    **时间戳**     |
| **token** | **string** | <font color=red>必需</font> | **无** |     **令牌**      |
| user_name |   string   | <font color=red>必需</font> |   无   |  用户名（学号）   |
| user_pwd  |   string   | <font color=red>必需</font> |   无   | md5加密的用户密码 |
|   lcode   |    int     | <font color=red>必需</font> |   无   |      验证码       |

```json
{
    "code":200,
    "msg":"登录成功！",
    "data":{
        "user_name":"*************",//用户名
        "user_contacte":"***********",//用户联系方式
        "user_rtime":"1501212145",//用户注册时间
    },
}
```

```json
{
    "code":400,
    "msg":"（看返回的具体错误信息）",
    "data":"",
}
```

### Upload Icon

> <font color=red>`post`</font>  api.tp5.com/user/icon

|   参数    |    类型    |          必需/可选          |  默认  |       描述        |
| :-------: | :--------: | :-------------------------: | :----: | :---------------: |
| **time**  |  **int**   | <font color=red>必需</font> | **无** |    **时间戳**     |
| **token** | **string** | <font color=red>必需</font> | **无** |     **令牌**      |
| user_name |   string   | <font color=red>必需</font> |   无   |  用户名（学号）   |
| user_icon |   string   | <font color=red>必需</font> |   无   | 用户头像(默认200*200) |

```json
{
    "code":200,
    "msg":"头像设置成功！",
    "data":"/uploads/icon/usernameicon.jpg"
}
```

```json
{
    "code":400,
    "msg":"（看返回的具体错误信息）",
    "data":"",
}
```
