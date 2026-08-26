<?php

namespace App\Support;

use App\Models\AdminNotification;
use App\Models\MailOutbox;

class Npa
{
    public const ADMIN_EMAIL = 'info@mongolec.org';

    public const POSITIONS = [
        'Туслах байгаль хамгаалагч',
        'Байгаль хамгаалагч',
        'Мэргэжилтэн',
        'Ахлах мэргэжилтэн/менежер',
        'Дарга/захирал',
    ];

    public const PARK_TYPES = [
        'Дархан цаазат газар',
        'Байгалийн цогцолборт газар',
        'Байгалийн нөөц газар',
        'Байгалийн дурсгалт газар',
    ];

    public const CONTRACT_TYPES = ['Гэрээт', 'Үндсэн', 'Дадлагажигч оюутан'];

    public const VOLUNTEER_DURATIONS = ['1 сар хүртэл', '1-3 сар', '3-6 сар', '6-12 сар', '1 жилээс дээш'];

    public const AIMAGS = [
        'Улаанбаатар', 'Архангай', 'Баян-Өлгий', 'Баянхонгор', 'Булган', 'Говь-Алтай',
        'Говьсүмбэр', 'Дархан-Уул', 'Дорноговь', 'Дорнод', 'Дундговь', 'Завхан',
        'Орхон', 'Өвөрхангай', 'Өмнөговь', 'Сүхбаатар', 'Сэлэнгэ', 'Төв',
        'Увс', 'Ховд', 'Хөвсгөл', 'Хэнтий',
    ];

    public const REGIONS = ['Баруун бүс', 'Хангайн бүс', 'Төвийн бүс', 'Зүүн бүс', 'Говийн бүс'];

    /** Кирилл үсгийн шалгалт (овог, нэр) */
    public const CYRILLIC_REGEX = '/^[А-ЯЁӨҮа-яёөү][А-ЯЁӨҮа-яёөү\s\-\.]*$/u';

    /** Утасны дугаарын шалгалт */
    public const PHONE_REGEX = '/^\+?[0-9]{6,15}$/';

    /**
     * Админд мэдэгдэл үүсгэж, info@mongolec.org руу и-мэйл (outbox) бүртгэнэ.
     * SMTP тохируулаагүй үед mail_outbox хүснэгтэд хадгалагдаж, админ панелд харагдана.
     */
    public static function notifyAdmin(string $kind, ?int $refId, string $text, string $mailSubject = null): void
    {
        AdminNotification::create([
            'kind' => $kind,
            'ref_id' => $refId,
            'text' => $text,
        ]);

        MailOutbox::create([
            'to' => self::ADMIN_EMAIL,
            'subject' => $mailSubject ?? $text,
            'body' => $text,
        ]);
    }
}
