<?php
global $encryption, $additionalParams, $host;

?>


<div class="rounded-lg shadow-md bg-gray-200 p-4 mb-4">
    <?php
    if (isset($_POST['new_password']) && isset($_POST['password']) && isset($_POST['repeat_password'])) {
        $newPassword = $_POST['new_password'];
        $password = $_POST['password'];
        $repeatPassword = $_POST['repeat_password'];
        if ($newPassword != $repeatPassword) {
            echo showError("Passwords do not match");
        } else {
            if (encodePassword($password) == $_GET['password']) {
                $time = time();
                file_put_contents(__DIR__ . "/../../../data/keys-$time.json", file_get_contents(__DIR__ . "/../../../data/keys.json"));
                $encryption->setPrivateKey(encodePassword($newPassword));
                $encryption->setPassword(encodePassword($newPassword));
            } else {
                echo showError("old Password do not match");
            }
        }
    }
    ?>
    <h3 class="font-bold text-xl mb-4">Change your password</h3>
    <small>All data will be backup to another file on data.</small>
    <form action="/v1/settings<?=$additionalParams?>" method="post">
        <div class="mt-2">
            <label for="price" class="block text-sm/6 font-medium text-gray-900">Your old password</label>
            <div class="mt-2">
                <div class="flex items-center rounded-md bg-white  outline outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">
                    <input type="password" name="password" id="price"
                           class="block min-w-0 grow py-1.5 pl-1 pr-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6">
                </div>
            </div>
        </div>
        <div class="mt-2">
            <label for="price" class="block text-sm/6 font-medium text-gray-900">New Password</label>
            <div class="mt-2">
                <div class="flex items-center rounded-md bg-white  outline outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">
                    <input type="password" name="new_password"
                           class="block min-w-0 grow py-1.5 pl-1 pr-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6">
                </div>
            </div>
        </div>
        <div class="mt-2">
            <label for="price" class="block text-sm/6 font-medium text-gray-900">Password repeat</label>
            <div class="mt-2">
                <div class="flex items-center rounded-md bg-white  outline outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">
                    <input type="password" name="repeat_password"
                           class="block min-w-0 grow py-1.5 pl-1 pr-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6">
                </div>
            </div>
        </div>
        <button class="bg-blue-500 text-white rounded-md mt-2 px-4 py-2 ">Change password</button>
    </form>
</div>

<a class="bg-blue-500 text-white rounded-md mt-4 px-4 py-2  hover:bg-blue-400" target="_blank" href="<?="/data/keys.json"?>" download>Download backup file</a>
