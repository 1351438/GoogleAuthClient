<?php
global $json, $additionalParams;

$reloadUntil = 30 - (time() % 30);
header("Refresh:$reloadUntil");

use Vectorface\GoogleAuthenticator;

if (isset($_GET['delete'])) {
    $temp = [];
    $deleteKey = $_GET['delete'];

    for ($i = 0; $i < count($json['keys']); $i++) {
        $item = $json['keys'][$i];
        $key = $item['key'];
        if ($key !== $deleteKey) {
            $temp[] = $item;
        }
    }
    $json['keys'] = $temp;
}
if (isset($json['keys']) && count($json['keys']) > 0) {
    $ga = new GoogleAuthenticator();
    for ($i = 0; $i < count($json['keys']); $i++) {
        $item = $json['keys'][$i];

        $name = $item['code_name'];
        $key_type = $item['key_type'];
        $key = $item['key'];

        try {
            $currentCode = $ga->getCode($key);
            $image = $ga->getQRCodeUrl($name, $key);
            echo <<<HTML
<div class="text-red-500 w-full mx-auto border-[2px] items-center mt-2 border-solid border-blue-500 px-8 py-8 rounded-[32px] bg-white  flex justify-between">
    <div class="flex gap-2 items-center">
        <img class="w-[100px] h-[100px]" src="$image"/>
        <div class="text-2xl font-bold text-orange-500 flex flex-col">
            $name
            <input readonly value="$key" class="text-sm font-normal text-black"/>
            <div class="flex gap-2">
                <span class="cursor-pointer text-sm font-bold text-white rounded-md flex flex-col px-4 py-2 bg-red-500 hover:bg-red-400" onclick="deleteItem('$key')">Delete</span>
            </div>
        </div>
    </div>
    <div class="text-2xl font-bold text-blue-500" style="letter-spacing: 4px;">
        $currentCode
    </div>
</div>
HTML;
        } catch (Exception $e) {
            echo showError($e->getMessage() . "\n" . $name . "-" . $key);
        }
    }
    echo "<script>";
    echo <<<JS
            function deleteItem(key) {
                if(confirm('Are you sure you want to delete this?')) {
                    location.href='v1/home$additionalParams&delete=' + key;
                }
            }
JS;

    echo "</script>";
} else {
    echo "You have no key!";
}