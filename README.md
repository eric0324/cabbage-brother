# Cabbage-brother

Cabbage-brother 是一個可以讓你查詢菜價的 Chatbot！


## 功能


- 查詢菜價



## 系統需求

基於 Laravel 5.8 版本開發

## 安裝
git clone 或是直接下載這份程式碼後，按照一般 Laravel 流程架設即可。

## 設定

### 1. Facebook 帳號登入

這是一隻 LINE Chatbot，所以先去 [LINE Developers](https://developers.line.biz/console/) 申請，並且取得 `channel secret` 和 `channel access token` 後，在 `.env` 中設定以下參數：

- CHANNEL_ACCESS_TOKEN
- CHANNEL_SECRET

## Roadmap

- 查詢菜價功能
  - 可以直接透過關鍵字查詢菜價


## Contributing

歡迎各位貢獻開發到本專案。

目前專案處於早期核心設計階段，除了明顯的 bug 歡迎直接送 PR 之外，請不要直接寫新功能的開發 PR


## License

Licensed under the GNU General Public License Version 3.0 or later.
