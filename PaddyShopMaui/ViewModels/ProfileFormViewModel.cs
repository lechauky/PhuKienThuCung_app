using System.Collections.ObjectModel;
using System.Globalization;
using System.Text.RegularExpressions;
using CommunityToolkit.Mvvm.ComponentModel;
using PaddyShop.Models;
using PaddyShop.Services;

namespace PaddyShop.ViewModels;

/// <summary>
/// Form thông tin khách hàng dùng chung cho Đăng ký và Sửa thông tin.
/// Chọn địa chỉ theo Tỉnh -> Huyện -> Xã giống trang register.php của website.
/// </summary>
public partial class ProfileFormViewModel(ShopApi api) : ObservableObject
{
    private bool _suppressCascade;

    [ObservableProperty] private string lastName = "";
    [ObservableProperty] private string firstName = "";
    [ObservableProperty] private string phone = "";
    [ObservableProperty] private string email = "";
    [ObservableProperty] private DateTime? birthday = new DateTime(2000, 1, 1);
    [ObservableProperty] private string street = "";
    [ObservableProperty] private bool isLoadingPlaces;

    public DateTime MaxBirthday { get; } = DateTime.Today;
    public DateTime MinBirthday { get; } = new(1920, 1, 1);

    public ObservableCollection<Place> Provinces { get; } = [];
    public ObservableCollection<Place> Districts { get; } = [];
    public ObservableCollection<Place> Wards { get; } = [];

    [ObservableProperty] private Place? selectedProvince;
    [ObservableProperty] private Place? selectedDistrict;
    [ObservableProperty] private Place? selectedWard;

    /// <summary>Tải danh sách tỉnh; nếu có khách (sửa hồ sơ) thì điền sẵn thông tin và chọn sẵn địa chỉ</summary>
    public async Task InitializeAsync(Customer? c = null)
    {
        IsLoadingPlaces = true;
        _suppressCascade = true;
        try
        {
            if (Provinces.Count == 0) Fill(Provinces, await api.ProvincesAsync());
            if (c != null)
            {
                LastName = c.LastName;
                FirstName = c.FirstName;
                Phone = c.Phone;
                Email = c.Email;
                Street = c.Street;
                Birthday = DateTime.TryParseExact(c.Birthday, "yyyy-MM-dd", CultureInfo.InvariantCulture, DateTimeStyles.None, out var d) ? d : Birthday;

                SelectedProvince = Provinces.FirstOrDefault(p => p.Id == c.ProvinceId);
                if (c.ProvinceId != null) Fill(Districts, await api.DistrictsAsync(c.ProvinceId));
                SelectedDistrict = Districts.FirstOrDefault(p => p.Id == c.DistrictId);
                if (c.DistrictId != null) Fill(Wards, await api.WardsAsync(c.DistrictId));
                SelectedWard = Wards.FirstOrDefault(p => p.Id == c.WardId);
            }
        }
        catch (ApiException e)
        {
            Notifier.Toast(e.Message);
        }
        finally
        {
            _suppressCascade = false;
            IsLoadingPlaces = false;
        }
    }

    private static void Fill(ObservableCollection<Place> target, IEnumerable<Place> items)
    {
        target.Clear();
        foreach (var i in items) target.Add(i);
    }

    // Chọn tỉnh -> tải huyện; chọn huyện -> tải xã
    async partial void OnSelectedProvinceChanged(Place? value)
    {
        if (_suppressCascade) return;
        SelectedDistrict = null;
        SelectedWard = null;
        Districts.Clear();
        Wards.Clear();
        if (value == null) return;
        try { Fill(Districts, await api.DistrictsAsync(value.Id)); }
        catch (ApiException e) { Notifier.Toast(e.Message); }
    }

    async partial void OnSelectedDistrictChanged(Place? value)
    {
        if (_suppressCascade) return;
        SelectedWard = null;
        Wards.Clear();
        if (value == null) return;
        try { Fill(Wards, await api.WardsAsync(value.Id)); }
        catch (ApiException e) { Notifier.Toast(e.Message); }
    }

    /// <summary>Kiểm tra dữ liệu theo đúng quy tắc phía máy chủ. Trả về câu lỗi hoặc null nếu hợp lệ.</summary>
    public string? Validate()
    {
        if (string.IsNullOrWhiteSpace(LastName)) return "Vui lòng nhập họ";
        if (LastName.Trim().Length > 50) return "Họ tối đa 50 ký tự";
        if (string.IsNullOrWhiteSpace(FirstName)) return "Vui lòng nhập tên";
        if (FirstName.Trim().Length > 10) return "Tên tối đa 10 ký tự";
        if (!Regex.IsMatch(Phone.Trim(), @"^0\d{9}$")) return "Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0";
        if (!Regex.IsMatch(Email.Trim(), @"^[^@\s]+@[^@\s]+\.[^@\s]+$")) return "Email không hợp lệ";
        if (Birthday == null || Birthday > DateTime.Today) return "Ngày sinh không hợp lệ";
        if (SelectedWard == null) return "Vui lòng chọn Tỉnh / Huyện / Xã";
        if (string.IsNullOrWhiteSpace(Street)) return "Vui lòng nhập địa chỉ cụ thể (số nhà, đường...)";
        return null;
    }

    public ProfileRequest ToRequest(string? username = null, string? password = null) => new(
        LastName.Trim(), FirstName.Trim(), Phone.Trim(), Email.Trim(),
        (Birthday ?? DateTime.Today).ToString("yyyy-MM-dd", CultureInfo.InvariantCulture),
        Street.Trim(), SelectedWard?.Id ?? "", username, password);
}
