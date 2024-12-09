<?php
$host = "http://" . $_SERVER['HTTP_HOST'] . "/";
function showError($msg)
{
    return <<<HTML
            <div class="px-4 py-4 my-2 w-full bg-red-500 rounded-md shadow-md text-white ">
            <b>Error: </b> $msg
</div>
HTML;

}

function encodePassword($password)
{
    $password = md5(base64_encode($password));
    return $password;
}

include __DIR__ . '/vendor/autoload.php';
include __DIR__ . "/lib/Router.php";
include __DIR__ . "/encryption.php";

$exp = explode("?", $_SERVER['REQUEST_URI']);
if ($exp[0] == "/") {
    header("Location: /v1/home");
}
$params = [];
if (count($exp) > 0)
    parse_str($exp[count($exp) - 1], $params);
$additionalParams = count($params) > 0 ? "?" . http_build_query($params) : "";


if (preg_match('/\.(?:png|jpg|jpeg|gif|js|json)$/', $_SERVER["REQUEST_URI"])) {
    require "proccess_otherfiles.php";
} else {
    $request = $_SERVER['REQUEST_URI'];
    date_default_timezone_set('UTC');
    ?>
    <html>
    <head>
        <base href="<?= $host ?>">
        <title>Google Authenticator</title>
        <link rel="icon" href="styles/logo.png"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="styles/3.4.16.js"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            clifford: '#da373d',
                        }
                    }
                }
            }
        </script>
    </head>
    <body>
    <header class="bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="#" class="-m-1.5 p-1.5">
                    <span class="sr-only">Two Factor</span>
                    <img class="h-8 w-auto" src="styles/logo.png"
                         alt="">
                </a>
            </div>
            <div class="hidden lg:flex lg:gap-x-12">
                <a href="v1/home<?= $additionalParams ?>" class="text-sm/6 font-semibold text-gray-900 <?= str_contains($request,'home') ? "!text-blue-500" : ""?> ">Codes</a>
                <a href="v1/new<?= $additionalParams ?>" class="text-sm/6 font-semibold text-gray-900 <?= str_contains($request,'new') ? "!text-blue-500" : ""?> ">New Code</a>
                <a href="v1/settings<?= $additionalParams ?>" class="text-sm/6 font-semibold text-gray-900 <?= str_contains($request,'settings')  ? "!text-blue-500" : ""?> ">Settings</a>
            </div>
            <div class="lg:hidden fixed bottom-0 right-0 left-0 h-[63px] bg-gray-900 flex justify-evenly items-center !text-white">
                <a href="v1/home<?= $additionalParams ?>" class="w-full h-full flex justify-center items-center text-sm/6 font-semibold <?= str_contains($request,'home') ? "bg-blue-500" : ""?>">Codes</a>
                <a href="v1/new<?= $additionalParams ?>" class="w-full h-full flex justify-center items-center text-sm/6 font-semibold <?= str_contains($request,'new') ? "bg-blue-500" : ""?>">New Code</a>
                <a href="v1/settings<?= $additionalParams ?>" class="w-full h-full flex justify-center items-center text-sm/6 font-semibold <?= str_contains($request,'settings')  ? "bg-blue-500" : ""?>">Settings</a>
            </div>
        </nav>
    </header>
    <div class="max-w-[650px] mx-auto p-4">
        <?php
        if (isset($_GET['password'])) {
            $password = $_GET['password'];
            if (strlen($password) != 32) {
                $password = md5(base64_encode($password));
                header("Location: ?password=$password");
            }
            $isValid = true;
            $encryption = new encryption($password, $password);
            if (is_file(__DIR__ . "/data/keys.json")) {
                $data = file_get_contents(__DIR__ . "/data/keys.json");
                $decrypted = $encryption->decrypt($data);
                $json = json_decode($decrypted, true);
                if (!is_array($json)) {
                    $isValid = false;
                    echo showError("Wrong json format.");
                }
            } else {
                $json = [];
            }

            if ($isValid) {
                $router = new Router($request);

                $router->version("v1", function () {
                    include __DIR__ . "/versions/v1/handler.php";
                });

                if (is_array($json)) {
                    $json = $encryption->encrypt(json_encode($json));
                    file_put_contents(__DIR__ . "/data/keys.json", $json);
                }
            } else {
                echo showError("Password is wrong.");
                goto password;
            }
        } else {
            password:
            echo <<<HTML
                <form method="get" action="$request">
                       <div class="mt-2">
                           <label for="price" class="block text-sm/6 font-medium text-gray-900">Password</label>
                           <div class="mt-2">
                               <div class="flex items-center rounded-md bg-white  outline outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">
                                   <input type="password" name="password" 
                                          class="block min-w-0 grow py-1.5 pl-1 pr-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6">
                               </div>
                           </div>
                       </div>
                        <button class="bg-blue-500 text-white rounded-md mt-2 px-4 py-2 ">Continue</button>
                </form>
HTML;

        }

        ?>

    </div>
    </body>
    </html>

<?php } ?>