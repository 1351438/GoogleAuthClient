<?php
global $json, $additionalParams;
if (isset($_POST['code']) && isset($_POST['key']) && isset($_POST['key_type'])) {
    $codeName = $_POST['code'];
    $key = $_POST['key'];
    $key_type = $_POST['key_type'];
    if (in_array($key_type, ["time", 'counter'])) {
        try {
            $ga = new \Vectorface\GoogleAuthenticator();
            $currentCode = $ga->getCode($key);
            $newKey = [
                "code_name" => $codeName,
                "key" => $key,
                "key_type" => $key_type,
            ];
            $add = true;
            if (isset($json['keys'])) {
                foreach ($json['keys'] as $addedKey) {
                    if ($addedKey['key'] == $key) {
                        $add = false;
                        echo showError("Key already exists");
                        break;
                    }
                }
            }
            if ($add) {
                $json['keys'][] = $newKey;
                header('Location: /v1/home' . $additionalParams);
            }
        } catch (Exception $e) {
            echo showError($e->getMessage());
        }
    } else {
        echo showError("Key type not allowed");
    }
}
?>
<div class="rounded-lg shadow-md bg-gray-200 p-4">
    <h3 class="font-bold text-xl mb-4">Enter code detail</h3>
    <form action="/v1/new<?= $additionalParams ?>" method="post">
        <div class="mt-2">
            <label for="price" class="block text-sm/6 font-medium text-gray-900">Code Name</label>
            <div class="mt-2">
                <div class="flex items-center rounded-md bg-white  outline outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">
                    <input type="text" name="code" id="price"
                           class="block min-w-0 grow py-1.5 pl-1 pr-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6">
                </div>
            </div>
        </div>
        <div class="mt-2">
            <label for="price" class="block text-sm/6 font-medium text-gray-900">Your key</label>
            <div class="mt-2">
                <div class="flex items-center rounded-md bg-white  outline outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">
                    <input type="text" name="key" id="price"
                           class="block min-w-0 grow py-1.5 pl-1 pr-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6">
                </div>
            </div>
        </div>
        <div class="mt-2">
            <label for="price" class="block text-sm/6 font-medium text-gray-900">Type of key</label>
            <div class="mt-2">
                <div class="flex items-center rounded-md bg-white  outline outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">
                    <select id="currency" name="key_type" aria-label="Currency"
                            class="col-start-1 row-start-1 w-full appearance-none rounded-md py-1.5 pl-3 pr-7 text-base text-gray-500 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                        <option value="time">Time based</option>
                        <option value="counter" disabled>Counter based</option>
                    </select>
                </div>
            </div>
        </div>
        <button class="bg-blue-500 text-white rounded-md mt-2 px-4 py-2 ">Add Key</button>
    </form>
</div>