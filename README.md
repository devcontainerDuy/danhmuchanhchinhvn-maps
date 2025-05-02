
# Danhmuchanhchinhvn Maps

Thư viện PHP hỗ trợ làm việc với bản đồ hành chính Việt Nam. Gói này cung cấp các công cụ để quản lý và nhập dữ liệu về tỉnh, huyện, xã trong Việt Nam sử dụng Laravel.

Dữ liệu được lấy trực tiếp từ [Tổng Cục Thống Kê Việt Nam](https://danhmuchanhchinh.gso.gov.vn/).

## Tính năng

- Nhập dữ liệu hành chính từ tệp Excel.
- Quản lý tỉnh, huyện, xã bằng các mô hình Eloquent.
- Tự động tạo bảng cơ sở dữ liệu và các mối quan hệ.
- Tùy chỉnh tên bảng và cột.

## Yêu cầu

- PHP 8.2 trở lên.
- Laravel Framework.
- Các thư viện cần thiết:
  - `maatwebsite/excel`
  - `guzzlehttp/guzzle`
  - `illuminate/support`
  - `illuminate/console`
  - `illuminate/database`

## Cài đặt

1. Cài đặt gói thông qua Composer:

   ```shell
   composer require khanh-duy/danhmuchanhchinhvn-maps
   ```

2. Xuất file cấu hình và file migration:

   ```shell
   php artisan vendor:publish --provider="KhanhDuy\DanhmuchanhchinhvnMaps\Providers\ServiceProvider"
   ```

## Cấu hình

File cấu hình `config/gso.php` cho phép bạn tùy chỉnh tên bảng và cột được sử dụng bởi gói. Cấu hình mặc định như sau:

1. Tables:

   ```php
   'tables' => [
       'provinces' => 'provinces',
       'districts' => 'districts',
       'wards'     => 'wards',
   ],
   ```

2. Columns:

   ```php
   'columns' => [
       'name' => 'name',
       'gso_id' => 'gso_id',
       'province_id' => 'province_id',
       'district_id' => 'district_id',
   ],
   ```

## Sử dụng

### Mô hình

Gói cung cấp ba mô hình Eloquent để quản lý dữ liệu hành chính:

- `Province`: Đại diện cho tỉnh.
- `District`: Đại diện cho huyện.
- `Ward`: Đại diện cho xã.

#### Ví dụ: Lấy danh sách huyện trong một tỉnh

```php
use KhanhDuy\DanhmuchanhchinhvnMaps\Models\Province;

$province = Province::find(1);
$districts = $province->district;
```

#### Ví dụ: Lấy danh sách xã trong một huyện

```php
use KhanhDuy\DanhmuchanhchinhvnMaps\Models\District;

$district = District::find(1);
$wards = $district->ward;
```

### Lệnh Artisan

Gói cung cấp hai lệnh Artisan để hỗ trợ cài đặt và nhập dữ liệu:

1. **Lệnh cài đặt**: Thiết lập gói và nhập dữ liệu hành chính.

   ```shell
   php artisan gso:install
   ```

2. **Lệnh nhập dữ liệu**: Tải xuống và nhập dữ liệu hành chính từ nguồn.

   ```shell
   php artisan gso:import
   ```

## Cấu trúc thư mục

Dưới đây là cấu trúc thư mục chính của gói:

- `src/Models`: Chứa các mô hình Eloquent (`Province`, `District`, `Ward`) để quản lý dữ liệu hành chính.
- `src/Imports`: Xử lý logic nhập dữ liệu từ tệp Excel (`GSOImports`).
- `src/Helpers`: Chứa các lớp hỗ trợ, ví dụ như lớp tải tệp (`DownloadFile`).
- `src/Console/Commands`: Chứa các lệnh Artisan (`InstallCommand`, `ImportCommand`) hỗ trợ cài đặt và nhập dữ liệu.
- `database/migrations`: Chứa các file migration để tạo bảng cơ sở dữ liệu.
- `config/gso.php`: File cấu hình cho phép tùy chỉnh tên bảng và cột.

## Tuỳ chỉnh thêm

Nếu bạn muốn tuỳ chỉnh sâu hơn (ví dụ: thêm cột, đổi tên quan hệ), bạn có thể:

1. **Chỉnh sửa file cấu hình**
2. **Mở rộng các mô hình Eloquent**

   ```php
   namespace App\Models;

   use KhanhDuy\DanhmuchanhchinhvnMaps\Models\Province as BaseProvince;

   class Province extends BaseProvince
   {
       public function customRelation()
       {
           return $this->hasMany(SomeOtherModel::class);
       }
   }
   ```

3. **Tuỳ chỉnh migration**: sau khi publish migration, chỉnh sửa trong `database/migrations`.

## Xử lý lỗi thường gặp

- ❗ **Không import được dữ liệu**
  → Kiểm tra kết nối internet và đảm bảo file Excel từ Tổng Cục Thống Kê vẫn còn tồn tại.

- ❗ **Thiếu table/cột khi migrate**
  → Đảm bảo bạn đã chạy `php artisan migrate` sau khi publish migration.

## Góp ý và đóng góp

Gói thư viện này **mở mã nguồn và chào đón đóng góp**!

Bạn có thể:

- Mở issue trên [GitHub repository](https://github.com/khanh-duy/danhmuchanhchinhvn-maps) (nếu có)
- Gửi pull request để cải thiện code
- Hoặc gửi email góp ý: **khanhduytran1803@gmail.com**

## Giấy phép

Dự án này được phát hành theo giấy phép **MIT License**.
Bạn có thể tự do sử dụng cho mục đích cá nhân hoặc thương mại.

## Liên hệ

- 📧 **Email:** khanhduytran1803@gmail.com
- 🌐 **GitHub:** [devcontainerDuy](https://github.com/devcontainerDuy)

🎉 **Cảm ơn bạn đã sử dụng Danhmuchanhchinhvn Maps!**
Nếu thấy hữu ích, hãy ⭐ dự án trên GitHub nhé!
