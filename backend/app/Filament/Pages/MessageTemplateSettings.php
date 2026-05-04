<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MessageTemplateSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'إعدادات القوالب';
    protected static ?string $title           = 'قوالب رسائل المراجعة';
    protected static ?int $navigationSort     = 10;
    protected static string $view             = 'filament.pages.message-template-settings';

    public string $approval_message  = '';
    public string $rejection_message = '';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function mount(): void
    {
        $this->approval_message  = Cache::get('tpl_approval_message',
            'تهانينا! تمت الموافقة على حساب المستخدم الخاص بك في منصة HTR. يمكنك الآن تسجيل الدخول والبدء.'
        );
        $this->rejection_message = Cache::get('tpl_rejection_message',
            'عذراً، تم رفض طلب التسجيل الخاص بك في منصة HTR.'
        );
        $this->form->fill([
            'approval_message'  => $this->approval_message,
            'rejection_message' => $this->rejection_message,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('approval_message')
                    ->label('رسالة القبول')
                    ->required()
                    ->rows(4)
                    ->helperText('هذه الرسالة تُرسل عبر البريد الإلكتروني عند قبول الحساب'),
                Textarea::make('rejection_message')
                    ->label('رسالة الرفض')
                    ->required()
                    ->rows(4)
                    ->helperText('هذه الرسالة تُرسل عبر البريد الإلكتروني عند رفض الحساب (يُضاف السبب تلقائياً)'),
            ])
            ->statePath('data');
    }

    public array $data = [];

    public function save(): void
    {
        $data = $this->form->getState();

        Cache::forever('tpl_approval_message',  $data['approval_message']);
        Cache::forever('tpl_rejection_message', $data['rejection_message']);

        $this->approval_message  = $data['approval_message'];
        $this->rejection_message = $data['rejection_message'];

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
