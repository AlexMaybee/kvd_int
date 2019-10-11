<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: text/html; charset=utf-8');


if ( CSite::InGroup (array(33) ) ) {
    $login = 'Sales';
    $pass = 'wwZ9HRPu';
    $a_folder = 'Sales/';
} else if(CSite::InGroup (array(34) )) {
    $login = 'Bitrix';
    $pass = 'U8mwAA0H';
    $a_folder = '';
}?>

<?if($_REQUEST['URL']):?>
<div style="padding: 50px;">
    <a href="#back" onclick="history.back();"><span class="adm-submenu-item-link-icon fileman_icon_folder_up" alt="Перейти на уровень выше"></span>Перейти на уровень выше</a>
</div>
<?endif;?>
<?
$link = 'ftp://'.$login.':'.$pass.'@sherp.net';

if($_REQUEST['URL']) {
    $url = 'ftp://'.$login.':'.$pass.'@sherp.net'.$_REQUEST['URL'];
} else {
    $url = 'ftp://'.$login.':'.$pass.'@sherp.net/B24/';
}

if (isset($_POST['CREATE_FOLDER'])) {
    if ($_POST['folder_name']) {
        createFolderFtp($url.$_POST['folder_name'].'/', $login, $pass);
    }
}


if (isset($_POST['FILE_UPLOAD'])) {
    if (!empty($_FILES['upload']['name'])) {
        $ch = curl_init();
        $localfile = $_FILES['upload']['tmp_name'];
        $fp = fopen($localfile, 'r');
        curl_setopt($ch, CURLOPT_URL, $url.$_FILES['upload']['name']);
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localfile));
        curl_exec ($ch);
        $error_no = curl_errno($ch);
        curl_close ($ch);
        if ($error_no == 0) {
            $error = 'File uploaded succesfully.';
        } else {
            $error = 'File upload error.';
        }
    } else {
        $error = 'Please select a file.';
    }
}?>


<?
if($_REQUEST['URL']):?>
    <div style="width: 100%">
        <form action="/archiveb24/disk/?URL=<?echo $_REQUEST['URL']?>" method="post" enctype="multipart/form-data" style="padding: 10px 50px;">
            <div>
                <label for="folder_name">Создать папку</label>
                <input name="folder_name" type="text" />
                <input type="submit" name="CREATE_FOLDER" value="Создать" />
            </div>
        </form>
    </div>

    <div style="width: 100%">
        <form action="/archiveb24/disk/?URL=<?echo $_REQUEST['URL']?>" method="post" enctype="multipart/form-data" style="padding: 10px 50px;">
            <div>
                <label for="upload">Выберите файл</label>
                <input name="upload" type="file" />
                <input type="submit" name="FILE_UPLOAD" value="Загрузить" />
            </div>
        </form>
    </div>
<?endif;?>

<?
$files = ftp_get_file_names($url, $login, $pass);
$list = [];
if(!empty($files)) {
    foreach ($files as $file) {

        if(CSite::InGroup (array(33)) && !strstr($file, 'Finances')) {
            if($file && !strstr($file, '#Recycle')) {
                $link = 'ftp://' . $login . ':' . $pass . '@sherp.net';
                if (strripos($file, '.')) {
                    $folder = getIconDiv($file);
                    $target = '_blank';
                    $request = $file;
                } else {
                    $folder = '<div class="js-disk-grid-open-folder bx-file-icon-container-small bx-disk-folder-icon"></div>';
                    $request = '?URL=' . $file . '/';
                    $target = '';
                    $link = '';
                }
                $list[] = [
                    'data' => [
                        "NAME" => $folder . '<a target="' . $target . '" href="' . $link . $request . '">' . getFName($file) . '</a>',
                    ]
                ];
            }
        } else if(CSite::InGroup (array(34))) {
            if($file && !strstr($file, '#Recycle')) {
                $link = 'ftp://' . $login . ':' . $pass . '@sherp.net';
                if (strripos($file, '.')) {
                    $folder = getIconDiv($file);
                    $target = '_blank';
                    $request = $file;
                } else {
                    $folder = '<div class="js-disk-grid-open-folder bx-file-icon-container-small bx-disk-folder-icon"></div>';
                    $request = '?URL=' . $file . '/';
                    $target = '';
                    $link = '';
                }
                $list[] = [
                    'data' => [
                        "NAME" => $folder . '<a target="' . $target . '" href="' . $link . $request . '">' . getFName($file) . '</a>',
                    ]
                ];
            }
        }

    }
}

$grid_options = new Bitrix\Main\Grid\Options('report_list');
$sort = $grid_options->GetSorting(['sort' => ['NAME' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();

$nav = new Bitrix\Main\UI\PageNavigation('report_list');
$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->initFromUri();


$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => 'report_list',
    'COLUMNS' => [
        ['id' => 'NAME', 'name' => 'Название', 'sort' => 'NAME', 'default' => true],
    ],
    'ROWS' => $list,
    'SHOW_ROW_CHECKBOXES' => true,
    'NAV_OBJECT' => $nav,
    'AJAX_MODE' => 'Y',
    'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
    'PAGE_SIZES' => [
        ['NAME' => "5", 'VALUE' => '5'],
        ['NAME' => '10', 'VALUE' => '10'],
        ['NAME' => '20', 'VALUE' => '20'],
        ['NAME' => '50', 'VALUE' => '50'],
        ['NAME' => '100', 'VALUE' => '200']
    ],
    'AJAX_OPTION_JUMP'          => 'N',
    'SHOW_CHECK_ALL_CHECKBOXES' => true,
    'SHOW_ROW_ACTIONS_MENU'     => true,
    'SHOW_GRID_SETTINGS_MENU'   => true,
    'SHOW_NAVIGATION_PANEL'     => true,
    'SHOW_PAGINATION'           => true,
    'SHOW_SELECTED_COUNTER'     => true,
    'SHOW_TOTAL_COUNTER'        => true,
    'SHOW_PAGESIZE'             => true,
    'SHOW_ACTION_PANEL'         => true,
    'ACTION_PANEL'              => [

    ],
    'ALLOW_COLUMNS_SORT'        => true,
    'ALLOW_COLUMNS_RESIZE'      => true,
    'ALLOW_HORIZONTAL_SCROLL'   => true,
    'ALLOW_SORT'                => true,
    'ALLOW_PIN_HEADER'          => true,
    'AJAX_OPTION_HISTORY'       => 'N'
]);?>



<?function ftp_get_file_names($url, $login, $pass)
{
    $file_names = array();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, FTP_CONNECTION_PORT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $login.":".$pass);

    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_DIRLISTONLY, TRUE);
    $files_list = curl_exec($ch);
    curl_close($ch);

    $file_names_array = explode("\n", $files_list);

    return $file_names_array;
}

function createFolderFtp($url, $login, $pass)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, FTP_CONNECTION_PORT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $login.":".$pass);
    curl_setopt($ch, CURLOPT_FTP_CREATE_MISSING_DIRS, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function getFName($name) {
    $file_name = '';
    $files_name = explode('/', $name);
    if(!empty($files_name)) {
        $file_name = $files_name[count($files_name)-1];
    }
    return $file_name;
}

function getIconDiv($name_file) {
    if(strstr($name_file, '.pdf')) {
        $icon = '<div class="bx-file-icon-container-small bx-disk-file-icon icon-pdf"></div>';
    } else if (strstr($name_file, '.xlsx') || strstr($name_file, '.xls')) {
        $icon = '<div class="bx-file-icon-container-small bx-disk-file-icon icon-xls"></div>';
    } else if (strstr($name_file, '.docx') || strstr($name_file, '.doc')) {
        $icon = '<div class="bx-file-icon-container-small bx-disk-file-icon icon-doc"></div>';
    } else {
        $icon = '<div class="bx-file-icon-container-small bx-disk-file-icon icon-img"></div>';
    }

    return $icon;
}



?>