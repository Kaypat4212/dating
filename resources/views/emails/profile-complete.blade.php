@component('mail::message')
# 🎉 Well Done, {{ $user->name }}! Your Profile is Complete!

Congratulations on finishing your profile — you're all set to start your journey! 💕

@component('mail::panel')
**Your profile is now live!** Here's what happens next:

✨ **You're visible** — other singles can now discover you  
💛 **Start matching** — swipe to find people you connect with  
💬 **Chat freely** — when you match, the conversation begins  
🔥 **Daily recommendations** — we'll suggest great matches for you
@endcomponent

Your complete profile means **better matches**, more visibility, and higher chances of finding that special someone. Way to go! 🙌

@component('mail::button', ['url' => $appUrl . '/swipe', 'color' => 'red'])
Start Swiping Now 💕
@endcomponent

**Pro tip:** Keep your profile fresh by updating your photos and bio regularly — it helps you stand out!

Ready to find your person? Let's do this! 🚀

Cheers,  
**The {{ $appName }} Team**

---
<small>You're receiving this because you completed your profile on {{ $appName }}. Manage your notification preferences in [account settings]({{ $appUrl }}/account/settings).</small>
@endcomponent
