<?php
header("Content-type:text/html;charset=gbk");
//define your token
define("TOKEN", "omnilab");

$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();


class wechatCallbackapiTest
{   
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        if (!empty($postStr)){
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $time = time();
            $msgType = $postObj->MsgType;
            
            $mysql = new SaeMysql();
            
            $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0</FuncFlag>
            </xml>"; 
            $webTplHead = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <ArticleCount>%d</ArticleCount>
            <Articles>";
            $webTplBody = "<item>
            <Title><![CDATA[%s]]></Title> 
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
            </item>";
            $webTplFoot = "</Articles>
            <FuncFlag>0</FuncFlag>
            </xml>";       
            
            switch ($msgType){
                case 'text':
                    $keyword = trim($postObj->Content);
                    /*$sql = "select state, vip from user_state where fromUsername = '$fromUsername'";
                    $result = $mysql->getData($sql);
                    $state = $result[0]['state'];
                    $vip = $result[0]['vip'];*/
                    /*$sql = "update user_state set state = '1' where fromUsername = '$fromUsername'";
                    $mysql->runSql($sql);*/

                    $post_string = '%7B%22records%22%3A%20%5B%7B%22OpenID%22%3A%20%22'.$fromUsername.'%22%2C%20%22发布时间%22%3A%20%22'.date('Y-m-d H:i',time()).'%22%2C%20%22评论内容%22%3A%20%22'.$keyword.'%22%7D%5D%2C%20%22force%22%3A%20true%2C%20%22method%22%3A%20%22insert%22%2C%20%22resource_id%22%3A%20%2261aab5bb-e5e2-455c-a4eb-77f504df1ce3%22%7D';
                    //$post_string = "{'records':[{'OpendID':".$fromUsername.",'发布时间':".date('Y-m-d H:i',time()).",'评论内容':".$keyword."}],'force':true,'method':'insert','resource_id':'61aab5bb-e5e2-455c-a4eb-77f504df1ce3'}";
                    $remote_server = 'http://202.121.178.242/api/3/action/datastore_upsert';
                    $context = array(
                        'http'=>array(
                            'method'=>'POST',
                            'header'=>'Authorization: 954c00c0-b01a-4863-a75b-1ed238d38f35',
                            'content'=>$post_string)
                        );
                    $stream_context = stream_context_create($context);
                    $data = file_get_contents($remote_server,FALSE,$stream_context);

                    $contentStr = $fromUsername.' '.$time.' '.$toUsername.' '.$keyword;
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
                    echo $resultStr;

                    break;

                case 'event':
                    $keyword = trim($postObj->Event);
                    switch ($keyword) {
                        case 'subscribe':
                            //mysql_set_charset("gbk");
                            //$sql = "insert into user_state values('$fromUsername','','0','0','0','0','0','')";
                            //$mysql->runSql($sql);

                            $post_string = '%7B%22records%22%3A%20%5B%7B%22OpenID%22%3A%20%22'.$fromUsername.'%22%2C%20%22关注时间%22%3A%20%22'.date('Y-m-d H:i',time()).'%22%7D%5D%2C%20%22force%22%3A%20true%2C%20%22method%22%3A%20%22insert%22%2C%20%22resource_id%22%3A%20%22d7c6b96c-7065-4e1f-9cae-70e2b9914be3%22%7D';
                            $remote_server = 'http://202.121.178.242/api/3/action/datastore_upsert';
                            $context = array(
                                'http'=>array(
                                    'method'=>'POST',
                                    'header'=>'Authorization: 954c00c0-b01a-4863-a75b-1ed238d38f35',
                                    'content'=>$post_string)
                                );
                            $stream_context = stream_context_create($context);
                            $data = file_get_contents($remote_server,FALSE,$stream_context);

                            $contentStr = "欢迎关注OMNILab\nOMNILab位于上海交通大学网络信息中心，是一个以技术和兴趣为导向的开放科研工作室，全称为开放移动网络与信息服务创新工作室";
                            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $contentStr);
                            echo $resultStr;

                            break;

                        case 'unsubscribe':
                            //$sql = "delete from user_state where fromUsername='$fromUsername'";
                            //$mysql->runSql($sql);
                            break;

                        default:
                            break;
                    }
                    break;

                case 'image':
                    break;   

                case 'voice':
                    break;

                case 'video':
                    break;  

                case 'location':  
                    break;

                case 'link':
                    break;

                default:
                    break;
            }
            $mysql->closeDb();

        }else {
            
        }
    }

}
?>