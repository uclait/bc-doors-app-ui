<?php
class PhotosController extends AppController
{
    var $name = 'Photos';
    var $uses = array('Photo');
    var $components = array('Date', 'File', 'GrouperSoapApi', 'Validate');

    public function index()
    {
        $this->autoRender = false;
    }
    public function download()
    {
        set_time_limit(SEARCH_TIMEOUT);
        $this->autoRender = false;
        $this->autoLayout = false;

        $userId = $this->Session->read('id');
        $debug = $this->Param->url("debug", 0) == 1;
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;

        if (DEBUG_WRITE) {$this->Debug->write("Start App");};

        $prefixes = array("Photo");
        $minimumFields = array(array(array("uid", "UID", "numeric")));

        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
        {
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT -  Photo Download Start|" . http_build_query($form[$prefixes[0]], '', '|'));};
            $this->Audit->user("Photo Download", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT -  Photo Download End");};
        }

        $filePath = "";
        $appValues = Cache::read(CACHE_NAME_APPLICATION);

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;
        if ($continue)
        {
            $uid = $form[$prefixes[0]]['uid'];
            if (strlen($uid) <> 9)
            {
                $continue = false;
                $errors[] = "Invalid UID";
                $this->response->statusCode(400);
            }
            else if (isset($appValues['photo']['download']['login'][$userId]))
            {
                if (DEBUG_WRITE) {$this->Debug->write("Start Get Membership");};
                $doorPlanId = $appValues['photo']['download']['login'][$userId];
                $data = $this->GrouperSoapApi->getMemberships($uid);
                $data = $this->Array->findByKey($data, "name", $doorPlanId);
                if (DEBUG_WRITE) {$this->Debug->write("End Get Membership");};

                //$data = $this->GrouperApi->getMembers($doorPlanId);
                //$data = $this->Array->findByKey($data, $this->GrouperApi->attributeNames[0], $uid);

                if (sizeof($data) == 0)
                {
                    $errors[] = "`{$userId}` does not have access to `{$uid}`";
                    $this->response->statusCode(403);
                    $continue = false;
                }
            }
            else
            {
                $errors[] = "`{$userId}` does not have access to this application";
                $this->response->statusCode(403);
                $continue = false;
            }
            if ($continue)
            {
                $api = $appValues['api']['card_holder_info'];
                $photoMount = (IS_WINDOWS ? substr(__FILE__, 0, 1) . ":" : "") . $appValues['photo']['download']['mount'];
		$this->Debug-write("photomount is  |{$photoMount}");

                if (DEBUG_WRITE) {$this->Debug->write("Start Search API");};
                $response = $this->Http->get($api['url'], array($api['param'] => $uid));
		$this->Debug->write("UID is {$uid}");
                $this->Debug->write("response is {$response}");
                $this->Debug->write("Status is {$this->Http->status}");
                if (DEBUG_WRITE) {$this->Debug->write("End Search API");};
                $this->Debug->write("API Response is {$this->Http->content}");
                if ($this->Http->status == $this->Http->STATUS_CODE_OK)
                {
                    $response = $this->Http->content;
                    // $xml = $this->Xml->load($response);
                    libxml_use_internal_errors(rue);
                    $xml = simplexml_load_string($response);
                    if ($xml === false) {
                        echo "Failed loading XML\n";
                        foreach(libxml_get_errors() as $error) {
                            echo "\t", $error->message;
                        }
                    }
                    // $fileParts = explode("\\\\", $xml->PhotoPath);
                    
                    $this->Debug->write("PhotoPath is {$xml->PhotoPath}");
                    $fileParts = explode("\\", $xml->PhotoPath);
                    $this->Debug->write("fileParts[1] is {$fileParts[1]}");
                    $this->Debug->write("fileParts[2] is {$fileParts[2]}");
                    $this->Debug->write("fileParts[3] is {$fileParts[3]}");


                     if ($fileParts[2] == "bc")
                     {
                         $filePath = "/mnt/bcphotos/iid/".$fileParts[5];
                     }
                     else
                     {
                         $filePath = str_replace("\\\\", "/", $xml->PhotoPath);
                         $photoParts = explode(":", $filePath);
                         if (sizeof($photoParts) > 1) {
                             $filePath = $photoMount . $photoParts[1];
                         }
                     }
                     if ($response)
                     {
                         $this->Debug->write("*********************");
                         $this->Debug->write("PhotoPath is {$PhotoPath}");
                         $this->Debug->write("*********************");
                         $photoParts = explode("/", $filePath);
                         $this->Debug->write("filePath is {$filePath}");
                         $this->Debug->write("*********************");
                         $this->Debug->write("photoParts[1] is {$photoParts[1]}");
                         $this->Debug->write("photoParts[2] is {$photoParts[2]}");
                         $this->Debug->write("photoParts[3] is {$photoParts[3]}");
                         if (sizeof($photoParts) > 1)
                         {
                             $this->Debug->write("Parts found! filePath is {$filePath}");
                             if (!file_exists($filePath))
                             {
                                 $errors[] = "`{$filePath} does not exist for Card Holder `{$uid}`";
                                 $this->response->statusCode(404);
                                 $continue = false;
                             }
                             else
                             {
                                 $this->response->header(array('Content-Transfer-Encoding' => 'binary',
                                                               'Content-Length' => filesize($filePath),
                                                               'Cache-Control' => 'no-cache'));
                             }
                         }
                         else
                         {
                             $filePath = "";
                             $errors[] = "Photo Path is empty for Card Holder `{$uid}`";
                             $this->response->statusCode(404);
                             $continue = false;
                         }
                     }
                     else
                     {
                         $errors[] = "Card Holder `{$uid}` was not found";
                         $this->response->statusCode(404);
                         $continue = false;
                     }
                 }
             }
         }

        if (AUDIT_ACTIVITY)
        {
            if (sizeof($errors) > 0)
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Download Failed Start|{$userId}|errors=" . sizeof($errors));};
                $this->Audit->user("Photo Download Failed", $userId, null, join(",", $errors));
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Download Failed End|{$userId}");};
            }
            else
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Download Success Start|{$userId}");};
                $this->Audit->user("Photo Download Success", $userId);
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Download Success End|{$userId}");};
            }
        }
        if (sizeof($errors) > 0)
        {
            //$this->response->statusCode(404);
            //throw new NotFoundException();
        }
        else
        {
            $this->response->type('jpg');
            $this->response->file($filePath);
        }

        if (DEBUG_WRITE) {$this->Debug->write("End App");};
    }
    /*
   public function download()
    {
        $this->autoRender = false;
        $this->autoLayout = false;

        $userId = $this->Session->read('id');
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;
        $prefixes = array("Photo");
        $minimumFields = array(array(array("uid", "UID", "numeric")));

        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
            $this->Audit->user("Photo Download", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));

        $filePath = "";
        $results = array();

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;
        if ($continue)
        {
            if (isset($_SERVER['HTTP_SIG']))
            {
                $continue = self::_verifySignature($_SERVER['HTTP_SIG'], $params);
            }
            else
            {
                $continue = false;
            }
            if ($continue)
            {
                if (SERVER_NAME == 'bcdoors-dev.it.ucla.edu')
                    $validUIDs = array('777777777' => 'c:/var/www/bcdoors.it.ucla.edu/cfs/app/webroot/img/777777777.jpg',
                                       '888888888' => 'c:/var/www/bcdoors.it.ucla.edu/cfs/app/webroot/img/chrysanthemum.jpg',
                                       '999999999' => 'c:/var/www/bcdoors.it.ucla.edu/cfs/app/webroot/img/hydrangeas.jpg');
                else
                    $validUIDs = array('777777777' => '/var/www/bcdoors.it.ucla.edu/cfs/app/webroot/img/777777777.jpg',
                                       '888888888' => '/var/www/bcdoors.it.ucla.edu/cfs/app/webroot/img/chrysanthemum.jpg',
                                       '999999999' => '/var/www/bcdoors.it.ucla.edu/cfs/app/webroot/img/hydrangeas.jpg');

                $uid = $form[$prefixes[0]]['uid'];
                if (isset($validUIDs[$uid]))
                {
                    $filePath = $validUIDs[$uid];
                    if (file_exists($filePath))
                    {
                        $this->response->header(array('Content-Transfer-Encoding' => 'binary',
                                                      'Content-Length' => filesize($filePath),
                                                      'Cache-Control' => 'no-cache'));
                    }
                    else
                        $errors[] = "`{$filePath} does not exist for Card Holder `{$uid}`";
                }
                else
                {
                    $errors[] = "Card Holder `{$uid}` was not found";
                    $this->response->statusCode(404);
                }
            }
            else
            {
                $errors[] = "Invalid signature";
                $this->response->statusCode(403);
            }
        }

        if (AUDIT_ACTIVITY)
        {
            if (sizeof($errors) > 0)
                $this->Audit->user("Photo Download Failed", $userId, null, join(",", $errors));
            else
                $this->Audit->user("Photo Download Success", $userId);
        }
        if (sizeof($errors) > 0)
        {
            //$this->response->statusCode(404);
            //throw new NotFoundException();
        }
        else
        {
            $this->response->type('jpg');
            $this->response->file($filePath);
        }
    }
    */
}
?>
