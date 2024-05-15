# denlogue
[ここから](http://153.126.171.221/login.php)アクセス可能
- もともと学内専用SNSとして作成したものを成果物公開用に変更した

## 使い方
1. 上のリンクにアクセス
2. 新規登録からアカウントを作成
3. 作成したアカウント情報からログイン
4. +で投稿可能
5. いいねやリツイート、投稿に入ってコメントの追加等基本的な~~Twitter~~ SNSの機能を利用可能
- その他
  - フォローすることでフォローした人の投稿のみを表示するフォロータイムラインを利用可能
  - PC版とスマホ版に対応
  - パスワードはハッシュ化

## DB構造
### DB一覧
- account(アカウント情報管理用)
- follow(フォロー関係管理用)
- post(投稿情報管理用)
- Good(いいね関係管理用)
- notice(通知管理用)

#### account
| 役割 | field名 | data型 | 桁数 | 備考 |
| :---: | :---: | :---: | :---: | :---: |
| ユーザID | user_id | int | - | 主キー&オートナンバー |
| ログインID | log_id | varchar | 20 | 重複なし |
| ユーザ名 | name | varchar | 20 | 重複あり |
| パスワード | pass | varchar | 255 | - |
| プロフィール | prof | varchar | 160 | NULL |
| プロフィール画像 | image | mediumblob | - | NULL |
| 画像の拡張子 | dot | varchar | 5 | NULL |
| 通知数 | notice | int | - | 0 |
| フォロー数 | follow | int | - | 0 |
| フォロワー数 | follower | int | - | 0 |

#### follow
| 役割 | field名 | data型 | 桁数 | 備考 |
| :---: | :---: | :---: | :---: | :---:|
| 主キー用ID | id | int | - | 主キー&オートナンバー |
| フォロー元 | follower | int | - | 外部(user_id) |
| フォロー先 | follow | int | - | 外部(user_id) |

#### post
| 役割 | field名 | data型 | 桁数 | 備考 |
| :---: | :---: | :---: | :---: | :---: |
| 投稿番号 | post_num | int | - | 主キー&オートナンバー |
| 投稿内容 | text | varchar | 140 | - |
| ユーザID | user_id | int | - | 外部(user_id) |
| いいね数 | good | int | - | - |
| リツイート数 | retweet | int | - | - |
| 投稿日時 | date | datetime | - | - |
| 投稿画像 | p_image | mediumblob | - | - |
| 投稿画像の拡張子 | p_dot | varchar5 | 5 | - |
| コメント数 | comment | int | - | - |
| 親投稿番号 | parent | int | - | 親なら0, 子なら親投稿番号 |
| リツイート元の投稿番号 | ret_num | int | - | - |

#### Good
| 役割 | field名 | data型 | 桁数 | 備考 |
| :---: | :---: | :---: | :---: | :---: |
| ID | id | int | - | 主キー&オートナンバー |
| ユーザID | user_id | int | - | - |
| 投稿番号 | post_num | int | - | - |

#### notice
- フラグ0はいいね, 1はリツイート, 2はコメント

| 役割 | field名 | data型 | 桁数 | 備考 |
| :---: | :---: | :---: | :---: | :---: |
| ID | id | int | - | 主キー&オートナンバー |
| ユーザID | user_id | int | - | - |
| 送信側ユーザID | send_id | int | - | - |
| 通知判定フラグ | flag | int | - | - |
| 投稿番号 | post_num | int | - | - |
