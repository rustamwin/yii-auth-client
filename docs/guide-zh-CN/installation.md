安装
====

## 安装扩展

要安装该扩展，请使用 Composer。运行
                                            
```
composer require --prefer-dist yiisoft/yii-auth-client "~3.0.0"
```

或在你的 composer.json 文件的“require”一节添加以下代码：

```json
"yiisoft/yii-auth-client": "~3.0.0"
```

## 配置应用程序

该扩展安装后，你需要设置验证客户端集合应用程序组件：

```php
return [
    'components' => [
        'authClientCollection' => [
            '__class' => 'Yiisoft\Yii\AuthClient\Collection',
            'clients' => [
                'google' => [
                    '__class' => 'Yiisoft\Yii\AuthClient\Clients\Google',
                    'clientId' => 'google_client_id',
                    'clientSecret' => 'google_client_secret',
                ],
                'facebook' => [
                    '__class' => 'Yiisoft\Yii\AuthClient\Clients\Facebook',
                    'clientId' => 'facebook_client_id',
                    'clientSecret' => 'facebook_client_secret',
                ],
                // etc.
            ],
        ]
        // ...
    ],
    // ...
];
```

提供了以下几个立即可用的客户端：

- [[\Yiisoft\Yii\AuthClient\Clients\Facebook|Facebook]].
- [[Yiisoft\Yii\AuthClient\Clients\GitHub|GitHub]].
- Google (通过 [[Yiisoft\Yii\AuthClient\Clients\Google|OAuth]] 和 [[Yiisoft\Yii\AuthClient\Clients\GoogleHybrid|OAuth Hybrid]]).
- [[Yiisoft\Yii\AuthClient\Clients\LinkedIn|LinkedIn]].
- [[Yiisoft\Yii\AuthClient\Clients\Live|Microsoft Live]].
- [[Yiisoft\Yii\AuthClient\Clients\Twitter|Twitter]].
- [[Yiisoft\Yii\AuthClient\Clients\VKontakte|VKontakte]].
- [[Yiisoft\Yii\AuthClient\Clients\Yandex|Yandex]].

配置每个客户端稍有不同。对于 OAuth 客户端需要从服务端获取客户端 ID 和密钥。而对于 OpenID 客户端，大多数情况下不需要调整。

## 保存授权数据

为了识别由外部服务验证的用户，我们需要保存首次验证时获得的 ID，以及用于接下来验证时检查该 ID。
并不建议将登录选项限于只能用外部登录，自身却不提供一种供用户登录的方法。
最好的做法是既提供原始的账号\密码登录方式，也提供外部验证方式。

如果我们要将用户信息存入数据库，则数据库模式可以是：

```php
class m??????_??????_auth extends \Yiisoft\Db\Migration
{
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'auth_key' => $this->string()->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('auth', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'source' => $this->string()->notNull(),
            'source_id' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('fk-auth-user_id-user-id', 'auth', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('auth');
        $this->dropTable('user');
    }
}
```

上述 SQL 中 `user` 表在高级项目模板中用于保存用户信息。
每个用户可以由多重外部服务验证，因此每个 `user` 记录可以关联多个 `auth` 记录。
在 `auth` 表中 `source` 是验证提供商的名称 `source_id` 是外部服务在该用户成功登录后提供的唯一 ID。

使用上述创建的数据表后我们可以生成 `Auth` 模型。无须进一步调整。

