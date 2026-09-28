namespace PaddyShop.Services;

/// <summary>Hiện thông báo ngắn (Toast của Android) và hộp thoại</summary>
public static class Notifier
{
    public static void Toast(string message)
    {
        MainThread.BeginInvokeOnMainThread(() =>
        {
#if ANDROID
            Android.Widget.Toast.MakeText(Platform.AppContext, message, Android.Widget.ToastLength.Short)?.Show();
#else
            _ = Shell.Current?.DisplayAlert("Thông báo", message, "OK");
#endif
        });
    }

    public static Task AlertAsync(string title, string message) =>
        Shell.Current.DisplayAlert(title, message, "OK");

    public static Task<bool> ConfirmAsync(string title, string message, string accept = "Đồng ý", string cancel = "Hủy") =>
        Shell.Current.DisplayAlert(title, message, accept, cancel);

    public static Task<string?> PromptAsync(string title, string message, string placeholder = "") =>
        Shell.Current.DisplayPromptAsync(title, message, "Đồng ý", "Hủy", placeholder, maxLength: 200);
}
