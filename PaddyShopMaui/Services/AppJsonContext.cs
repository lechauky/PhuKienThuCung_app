using System.Text.Json;
using System.Text.Json.Serialization;
using PaddyShop.Models;

namespace PaddyShop.Services;

/// <summary>
/// JSON source generator: tạo sẵn mã (de)serialize lúc build, chạy nhanh và an toàn khi bật trimming ở bản Release.
/// Mỗi kiểu dữ liệu gửi/nhận qua API cần được khai báo ở đây.
/// </summary>
[JsonSourceGenerationOptions(
    PropertyNamingPolicy = JsonKnownNamingPolicy.CamelCase,
    PropertyNameCaseInsensitive = true,
    DefaultIgnoreCondition = JsonIgnoreCondition.WhenWritingNull,
    NumberHandling = JsonNumberHandling.AllowReadingFromString)]
[JsonSerializable(typeof(ApiResponse<HomeData>))]
[JsonSerializable(typeof(ApiResponse<List<Category>>))]
[JsonSerializable(typeof(ApiResponse<List<Brand>>))]
[JsonSerializable(typeof(ApiResponse<Paged<Product>>))]
[JsonSerializable(typeof(ApiResponse<Product>))]
[JsonSerializable(typeof(ApiResponse<List<Place>>))]
[JsonSerializable(typeof(ApiResponse<UsernameCheck>))]
[JsonSerializable(typeof(ApiResponse<AuthResult>))]
[JsonSerializable(typeof(ApiResponse<Customer>))]
[JsonSerializable(typeof(ApiResponse<TokenOnly>))]
[JsonSerializable(typeof(ApiResponse<Cart>))]
[JsonSerializable(typeof(ApiResponse<Order>))]
[JsonSerializable(typeof(ApiResponse<Paged<Order>>))]
[JsonSerializable(typeof(ApiResponse<NotificationList>))]
[JsonSerializable(typeof(ApiResponse<ShopInfo>))]
[JsonSerializable(typeof(ApiResponse<JsonElement>))]
[JsonSerializable(typeof(Customer))]
[JsonSerializable(typeof(LoginRequest))]
[JsonSerializable(typeof(ProfileRequest))]
[JsonSerializable(typeof(ChangePasswordRequest))]
[JsonSerializable(typeof(EmailRequest))]
[JsonSerializable(typeof(ResetPasswordRequest))]
[JsonSerializable(typeof(CartRequest))]
[JsonSerializable(typeof(CheckoutRequest))]
[JsonSerializable(typeof(CancelOrderRequest))]
[JsonSerializable(typeof(ReadNotificationRequest))]
[JsonSerializable(typeof(EmptyRequest))]
public partial class AppJsonContext : JsonSerializerContext
{
}
