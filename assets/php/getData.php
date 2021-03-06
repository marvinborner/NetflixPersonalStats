<?php
/**
 * Server-side script of the Netflix Stats Generator to get the personal Netflix JSON
 * @author Marvin Borner
 * @copyright Marvin Borner 2018
 */

$debug = false;
$cookie = $_POST['cookie'];

if ($debug) {
    print_r(file_get_contents("debug.json"));
} else if (isset($cookie)) {
    $isLastPage = false;
    $currentPage = 0;
    $result = '[';

    while ($isLastPage === false) {
	// netflix.appContext.state.model.models.serverDefs.data.BUILD_IDENTIFIER
        $ch = curl_init('https://www.netflix.com/api/shakti/vf10970d2/viewingactivity?pg=' . $currentPage . '&pgSize=100');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        $answer = curl_exec($ch);

        if ($isLastPage = (count(json_decode($answer, true)['viewedItems']) > 0)) {
            $isLastPage = false;
            $result .= json_encode(json_decode($answer, true)['viewedItems']) . ',';
        } else {
            $isLastPage = true;
            $result = substr($result, 0, -1);
        }

        curl_close($ch);
        $currentPage++;
    }

    if ($result !== '') {
        print_r($result . ']');
    } else {
        http_response_code(404);
        die();
    }
} else {
    http_response_code(404);
    die();
}



