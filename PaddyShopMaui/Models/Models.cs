using System.Globalization;
using System.Text.Json.Serialization;
using CommunityToolkit.Mvvm.ComponentModel;

namespace PaddyShop.Models;

/// <summary>Định dạng tiền, ngày theo kiểu Việt Nam</summary>
public static class Fmt
{
    private static readonly CultureInfo Vi = new("vi-VN");

    /// <summary>72000 -> "72.000đ"</summary>
    public static string Vnd(long value) => value.ToString("#,##0", Vi) + "đ";

    /// <summary>"2026-09-21 19:46:58" -> "21/09/2026 19:46"</summary>
    public static string DateTime(string? raw)
    {
        if (string.IsNullOrWhiteSpace(raw)) return "—";
        return System.DateTime.TryParse(raw, CultureInfo.InvariantCulture, DateTimeStyles.None, out var d)
            ? d.ToString("dd/MM/yyyy HH:mm")
            : raw;
    }
}

/// <summary>Khung phản hồi chung của API: { success, message, data }</summary>
public class ApiResponse<T>
{
    public bool Success { get; set; }
    public string? Message { get; set; }
    public T? Data { get; set; }
}

public class Paged<T>
{
    public List<T> Items { get; set; } = [];
    public int Page { get; set; }
    public int PageSize { get; set; }
    public int Total { get; set; }
    public int TotalPages { get; set; }
}

public class Category
{
    public string Id { get; set; } = "";
    public string Name { get; set; } = "";
}

public class Brand
{
    public string Id { get; set; } = "";
    public string Name { get; set; } = "";
    public string? LogoUrl { get; set; }
}

public class Banner
{
    public int Id { get; set; }
    public string? ImageUrl { get; set; }
    public string? Title { get; set; }
    public string? ProductId { get; set; }
}

public class Product
{
    public string Id { get; set; } = "";
    public string Name { get; set; } = "";
    public long Price { get; set; }
    public long FinalPrice { get; set; }
    public string? DiscountType { get; set; }   // "amount" | "percent" | null
    public long? DiscountValue { get; set; }
    public int Stock { get; set; }
    public string? ImageUrl { get; set; }
    public string? BrandId { get; set; }
    public string? BrandName { get; set; }
    public string? CategoryId { get; set; }
    public string? CategoryName { get; set; }
    public string? Description { get; set; }
    public List<Product>? Related { get; set; }

    [JsonIgnore] public bool HasDiscount => FinalPrice < Price;
    [JsonIgnore] public bool InStock => Stock > 0;
    [JsonIgnore] public bool OutOfStock => Stock <= 0;
    [JsonIgnore] public string FinalPriceText => Fmt.Vnd(FinalPrice);
    [JsonIgnore] public string PriceText => Fmt.Vnd(Price);
    [JsonIgnore] public string StockText => Stock > 0 ? $"Còn {Stock} sản phẩm" : "Tạm hết hàng";
    [JsonIgnore]
    public string DiscountLabel => !HasDiscount ? ""
        : DiscountType == "percent" ? $"-{DiscountValue}%"
        : "-" + ((Price - FinalPrice) >= 1000 ? $"{(Price - FinalPrice) / 1000}K" : $"{Price - FinalPrice}");
    [JsonIgnore]
    public string DescriptionText => string.IsNullOrWhiteSpace(Description) ? "Chưa có mô tả" : Description.Trim();
}

public class HomeData
{
    public List<Banner> Banners { get; set; } = [];
    public List<Category> Categories { get; set; } = [];
    public List<Brand> TopBrands { get; set; } = [];
    public List<Product> BestSellers { get; set; } = [];
    public List<Product> OnSale { get; set; } = [];
    public List<Product> Newest { get; set; } = [];
}

public class Place
{
    public string Id { get; set; } = "";
    public string Name { get; set; } = "";
    public override string ToString() => Name;
}

public class Customer
{
    public string Id { get; set; } = "";
    public string LastName { get; set; } = "";
    public string FirstName { get; set; } = "";
    public string FullName { get; set; } = "";
    public string Phone { get; set; } = "";
    public string Email { get; set; } = "";
    public string Birthday { get; set; } = "";   // yyyy-MM-dd
    public string Username { get; set; } = "";
    public string Street { get; set; } = "";
    public string WardId { get; set; } = "";
    public string? WardName { get; set; }
    public string? DistrictId { get; set; }
    public string? DistrictName { get; set; }
    public string? ProvinceId { get; set; }
    public string? ProvinceName { get; set; }
    public string FullAddress { get; set; } = "";
    public string? AvatarUrl { get; set; }

    [JsonIgnore] public string UsernameText => "@" + Username;
    [JsonIgnore] public string NamePhone => $"{FullName}  |  {Phone}";
}

public class AuthResult
{
    public string Token { get; set; } = "";
    public Customer Customer { get; set; } = new();
}

public class TokenOnly
{
    public string Token { get; set; } = "";
}

public class UsernameCheck
{
    public string Username { get; set; } = "";
    public bool Available { get; set; }
}

public class CartItem
{
    public Product Product { get; set; } = new();
    public int Quantity { get; set; }
    public long LineTotal { get; set; }

    [JsonIgnore] public string LineTotalText => Fmt.Vnd(LineTotal);
    [JsonIgnore] public bool CanIncrease => Quantity < Product.Stock;
    [JsonIgnore] public string UnitLine => $"{Product.FinalPriceText} x {Quantity}";
}

public class Cart
{
    public List<CartItem> Items { get; set; } = [];
    public int ItemCount { get; set; }
    public int TotalQuantity { get; set; }
    public long Subtotal { get; set; }
    public long Discount { get; set; }
    public long Total { get; set; }

    [JsonIgnore] public string SubtotalText => Fmt.Vnd(Subtotal);
    [JsonIgnore] public string DiscountText => "-" + Fmt.Vnd(Discount);
    [JsonIgnore] public string TotalText => Fmt.Vnd(Total);
    [JsonIgnore] public bool HasDiscount => Discount > 0;
    [JsonIgnore] public string SubtotalLabel => $"Tạm tính ({TotalQuantity} sản phẩm)";
}

public class OrderItem
{
    public string ProductId { get; set; } = "";
    public string ProductName { get; set; } = "";
    public string? ImageUrl { get; set; }
    public int Quantity { get; set; }
    public long UnitPrice { get; set; }
    public long LineTotal { get; set; }

    [JsonIgnore] public string UnitLine => $"{Fmt.Vnd(UnitPrice)} x {Quantity}";
    [JsonIgnore] public string LineTotalText => Fmt.Vnd(LineTotal);
}

/// <summary>Status: 0 = Bị hủy, 1 = Đã giao, 2 = Đã xác nhận (Đang giao), 3 = Chưa xác nhận</summary>
public class Order
{
    public string Id { get; set; } = "";
    public string OrderDate { get; set; } = "";
    public string? DeliveryDate { get; set; }
    public int Status { get; set; }
    public string StatusText { get; set; } = "";
    public long Total { get; set; }
    public int? ItemCount { get; set; }
    public string? FirstImageUrl { get; set; }
    public string? Source { get; set; }
    public List<OrderItem>? Items { get; set; }
    public Customer? Shipping { get; set; }
    public string? Note { get; set; }
    public string? CancelReason { get; set; }
    public string? HandledBy { get; set; }
    public bool CanCancel { get; set; }

    [JsonIgnore] public string TotalText => Fmt.Vnd(Total);
    [JsonIgnore] public string OrderDateText => "Ngày đặt: " + Fmt.DateTime(OrderDate);
    [JsonIgnore] public string DeliveryDateText => "Ngày giao: " + Fmt.DateTime(DeliveryDate);
    [JsonIgnore] public bool IsDelivered => !string.IsNullOrEmpty(DeliveryDate);
    [JsonIgnore] public string ItemCountText => $"{ItemCount ?? 0} mặt hàng";
    [JsonIgnore] public string TitleText => "Mã đơn: " + Id;
    [JsonIgnore] public bool HasNote => !string.IsNullOrWhiteSpace(Note);
    [JsonIgnore] public bool HasCancelReason => Status == 0 && !string.IsNullOrWhiteSpace(CancelReason);
    [JsonIgnore] public string HandledByText => "Người xử lý: " + (HandledBy ?? (Status == 0 ? "Bạn đã tự hủy" : "—"));
    [JsonIgnore]
    public Color StatusColor => Status switch
    {
        0 => Color.FromArgb("#CC3333"),
        1 => Color.FromArgb("#0B84EE"),
        2 => Color.FromArgb("#2E7D32"),
        _ => Color.FromArgb("#F88C06"),
    };
}

public class NotificationItem
{
    public int Id { get; set; }
    public string Title { get; set; } = "";
    public string Content { get; set; } = "";
    public string? OrderId { get; set; }
    public bool IsRead { get; set; }
    public string CreatedAt { get; set; } = "";

    [JsonIgnore] public string CreatedAtText => Fmt.DateTime(CreatedAt);
    [JsonIgnore] public bool IsUnread => !IsRead;
    [JsonIgnore] public Color Background => IsRead ? Colors.White : Color.FromArgb("#FFF4F4");
}

public class NotificationList
{
    public List<NotificationItem> Items { get; set; } = [];
    public int Unread { get; set; }
}

public class ShopInfo
{
    public string Name { get; set; } = "";
    public string Company { get; set; } = "";
    public string Address { get; set; } = "";
    public string Hotline { get; set; } = "";
    public string Email { get; set; } = "";
    public string? LogoUrl { get; set; }
}

/* ----------------------------- Body gửi lên API ----------------------------- */

public record LoginRequest(string Username, string Password);

public record ProfileRequest(
    string LastName, string FirstName, string Phone, string Email, string Birthday,
    string Street, string WardId, string? Username = null, string? Password = null);

public record ChangePasswordRequest(string OldPassword, string NewPassword);
public record EmailRequest(string Email);
public record ResetPasswordRequest(string Email, string Code, string NewPassword);
public record CartRequest(string ProductId, int? Quantity = null);
public record CheckoutRequest(string? Note);
public record CancelOrderRequest(string OrderId, string? Reason);
public record ReadNotificationRequest(int? Id);
public record EmptyRequest();

/* ----------------------------- Dùng cho giao diện ----------------------------- */

/// <summary>Mục có thể chọn/bỏ chọn (chip lọc loại, thương hiệu, tình trạng đơn)</summary>
public partial class SelectableItem : ObservableObject
{
    public string Id { get; init; } = "";
    public string Name { get; init; } = "";

    [ObservableProperty]
    [NotifyPropertyChangedFor(nameof(ChipBackground), nameof(ChipText))]
    private bool isSelected;

    public Color ChipBackground => IsSelected ? Color.FromArgb("#CC3333") : Colors.White;
    public Color ChipText => IsSelected ? Colors.White : Color.FromArgb("#333333");
}
