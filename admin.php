<?php
$config_file = 'config.json';

// اگر فایل JSON موجود است، آن را بارگذاری می‌کنیم
if (file_exists($config_file)) {
    $config = json_decode(file_get_contents($config_file), true);
} else {
    $config = [
        'image' => 'your-ad-image.jpg',
        'font' => 'Aban',
        'movie_name' => '',
        'buttons' => [],
        'pages' => [],
        'ads' => [
            'top_left' => ['image' => '', 'link' => '', 'visible' => true],
            'top_right' => ['image' => '', 'link' => '', 'visible' => true]
        ]
    ];
}

// اگر فرم ارسال شد، مقادیر جدید را ذخیره می‌کنیم
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $config['image'] = $_POST['image'];
    $config['font'] = $_POST['font'];
    $config['movie_name'] = $_POST['movie_name'];

    // ذخیره بنرهای تبلیغاتی
    $config['ads']['top_left']['image'] = $_POST['top_left_image'];
    $config['ads']['top_left']['link'] = $_POST['top_left_link'];
    $config['ads']['top_left']['visible'] = isset($_POST['top_left_visible']);

    $config['ads']['top_right']['image'] = $_POST['top_right_image'];
    $config['ads']['top_right']['link'] = $_POST['top_right_link'];
    $config['ads']['top_right']['visible'] = isset($_POST['top_right_visible']);

    // ذخیره دکمه‌های جدید
    $config['buttons'] = [];
    if (!empty($_POST['button_text']) && !empty($_POST['button_link'])) {
        foreach ($_POST['button_text'] as $index => $button_text) {
            if (!empty($button_text) && !empty($_POST['button_link'][$index])) {
                $config['buttons'][] = [
                    'text' => $button_text,
                    'link' => $_POST['button_link'][$index],
                    'visible' => isset($_POST['button_visible'][$index])
                ];
            }
        }
    }

    // ذخیره صفحات جدید
    if (!empty($_POST['page_title'])) {
        foreach ($_POST['page_title'] as $index => $page_title) {
            if (!empty($page_title)) {
                $config['pages'][$index] = [
                    'title' => $page_title,
                    'content' => $_POST['page_content'][$index],
                    'visible' => isset($_POST['page_visible'][$index])
                ];
            }
        }
    }

    // ذخیره تغییرات در فایل JSON
    file_put_contents($config_file, json_encode($config));
    echo "<p>تنظیمات با موفقیت ذخیره شد!</p>";
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت تبلیغات و لینک‌ها</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            padding: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 13px;
            margin-top: 8px;
            box-sizing: border-box;
        }
        input[type="checkbox"] {
            margin-top: 12px;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .page-input {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h1>مدیریت لینک‌ها و تبلیغات</h1>

<form method="POST">
    <label for="movie_name">نام فیلم:</label>
    <input type="text" id="movie_name" name="movie_name" value="<?php echo $config['movie_name']; ?>">

    <label for="image">لینک تصویر تبلیغاتی:</label>
    <input type="text" id="image" name="image" value="<?php echo $config['image']; ?>">

    <label for="font">فونت دلخواه:</label>
    <select id="font" name="font">
        <option value="Aban" <?php echo $config['font'] == 'Aban' ? 'selected' : ''; ?>>Aban</option>
        <option value="Tahoma" <?php echo $config['font'] == 'Tahoma' ? 'selected' : ''; ?>>Tahoma</option>
        <option value="Vazir" <?php echo $config['font'] == 'Vazir' ? 'selected' : ''; ?>>Vazir</option>
        <option value="BYekan" <?php echo $config['font'] == 'BYekan' ? 'selected' : ''; ?>>BYekan</option>
    </select>

    <h2>مدیریت دکمه‌ها</h2>
    <div id="button-inputs">
        <?php foreach ($config['buttons'] as $index => $button): ?>
            <div class="button-input">
                <label for="button_text_<?php echo $index; ?>">متن دکمه:</label>
                <input type="text" id="button_text_<?php echo $index; ?>" name="button_text[]" value="<?php echo htmlspecialchars($button['text']); ?>">
                <label for="button_link_<?php echo $index; ?>">لینک دکمه:</label>
                <input type="text" id="button_link_<?php echo $index; ?>" name="button_link[]" value="<?php echo htmlspecialchars($button['link']); ?>">
                <label for="button_visible_<?php echo $index; ?>">فعال:</label>
                <input type="checkbox" id="button_visible_<?php echo $index; ?>" name="button_visible[<?php echo $index; ?>]" <?php echo $button['visible'] ? 'checked' : ''; ?>>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" id="add-button">افزودن دکمه جدید</button>

    <h2>مدیریت صفحات</h2>
    <div id="page-inputs">
        <?php foreach ($config['pages'] as $index => $page): ?>
            <div class="page-input">
                <label for="page_title_<?php echo $index; ?>">عنوان صفحه:</label>
                <input type="text" id="page_title_<?php echo $index; ?>" name="page_title[]" value="<?php echo htmlspecialchars($page['title']); ?>">
                <label for="page_content_<?php echo $index; ?>">محتوای صفحه:</label>
                <input type="text" id="page_content_<?php echo $index; ?>" name="page_content[]" value="<?php echo htmlspecialchars($page['content']); ?>">
                <label for="page_visible_<?php echo $index; ?>">فعال:</label>
                <input type="checkbox" id="page_visible_<?php echo $index; ?>" name="page_visible[<?php echo $index; ?>]" <?php echo $page['visible'] ? 'checked' : ''; ?>>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" id="add-page">افزودن صفحه جدید</button>

    <h2>مدیریت بنرهای تبلیغاتی</h2>

    <label for="top_left_image">تصویر بنر بالا چپ:</label>
    <input type="text" id="top_left_image" name="top_left_image" value="<?php echo $config['ads']['top_left']['image']; ?>">

    <label for="top_left_link">لینک بنر بالا چپ:</label>
    <input type="text" id="top_left_link" name="top_left_link" value="<?php echo $config['ads']['top_left']['link']; ?>">

    <label for="top_left_visible">نمایش بنر بالا چپ:</label>
    <input type="checkbox" id="top_left_visible" name="top_left_visible" <?php echo $config['ads']['top_left']['visible'] ? 'checked' : ''; ?>>

    <label for="top_right_image">تصویر بنر بالا راست:</label>
    <input type="text" id="top_right_image" name="top_right_image" value="<?php echo $config['ads']['top_right']['image']; ?>">

    <label for="top_right_link">لینک بنر بالا راست:</label>
    <input type="text" id="top_right_link" name="top_right_link" value="<?php echo $config['ads']['top_right']['link']; ?>">

    <label for="top_right_visible">نمایش بنر بالا راست:</label>
    <input type="checkbox" id="top_right_visible" name="top_right_visible" <?php echo $config['ads']['top_right']['visible'] ? 'checked' : ''; ?>>

    <button type="submit">ذخیره تغییرات</button>
</form>

<script>
    document.getElementById('add-button').addEventListener('click', function() {
        const buttonInputs = document.getElementById('button-inputs');
        const newButtonInput = document.createElement('div');
        newButtonInput.classList.add('button-input');
        newButtonInput.innerHTML = `
            <label>متن دکمه:</label>
            <input type="text" name="button_text[]" placeholder="متن دکمه">
            <label>لینک دکمه:</label>
            <input type="text" name="button_link[]" placeholder="لینک دکمه">
            <label>فعال:</label>
            <input type="checkbox" name="button_visible[]">
        `;
        buttonInputs.appendChild(newButtonInput);
    });

    document.getElementById('add-page').addEventListener('click', function() {
        const pageInputs = document.getElementById('page-inputs');
        const newPageInput = document.createElement('div');
        newPageInput.classList.add('page-input');
        newPageInput.innerHTML = `
            <label>عنوان صفحه:</label>
            <input type="text" name="page_title[]" placeholder="عنوان صفحه">
            <label>محتوای صفحه:</label>
            <input type="text" name="page_content[]" placeholder="محتوای صفحه">
            <label>فعال:</label>
            <input type="checkbox" name="page_visible[]">
        `;
        pageInputs.appendChild(newPageInput);
    });
</script>
<script type="text/javascript">
  var app_url = 'https://2ad.ir/';
  var app_api_token = 'f8f7fcea9590b1f383907a73691fe32900732f32';
  var app_advert = 0;
  var app_domains = ["365r.elhost.site"];
</script>
<script src="//2ad.ir/js/full-page-script.js"></script>
</body>
</html>
