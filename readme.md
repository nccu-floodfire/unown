# 未知圖騰（Unown）
未知圖騰為一編碼系統，設計目標為顯示題目(文章、圖片、音檔)並蒐集編碼結果的系統
- 2019/06/24
    當前版本為嘗試版本，僅支援引述標記，Model分為三個
    1. Encoder : 編碼員，每個編碼員會以一個UUID作為編碼員代碼，編碼題目(id)列表用逗點隔開，
    連續區間可以用冒號隔開e.g. 1,3,4-7,10，代表該編碼員需要編碼1,3,4,5,6,7,10這幾題
    2. Article : 引述標記的文章來源，id代表題目代碼，page_id代表文章獨立碼，會用在結果的辨別上，body為判斷內文
    3. Result : 編碼員的編碼結果，一篇文章可能有多個引述，故先設定為不唯一
    Result使用page_id而非id主要是方便對回水火NewsDiff的爬蟲編號，後續會修改掉

## 1. Clone unown
Git Clone from unown repository.

```
git clone https://github.com/nccu-floodfire/unown.git
```

## 2. 複製及設定環境參數檔
複製 `.env` 檔案
```bash
cd unown
cp .env.example .env
```

## 3. composer 安裝套件
在 unown 資料夾透過 composer 安裝需要的套件（記錄在 composer.json）。
```
$ composer install
```

## 4. 產生密鑰
```bash
php artisan key:generate
```

## 5. 資料庫 Migration
```bash 
# 回到專案目錄後
php artisan migrate
```
