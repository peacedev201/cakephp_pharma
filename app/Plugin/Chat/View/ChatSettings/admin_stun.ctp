<?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('chat','Video Conferencing'), array('controller' => 'chat_settings', 'action' => 'admin_stun'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Chat'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Chat', __d('chat','Video Conferencing'));?>
<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <?php echo $this->element('admin/setting');?>
                            <div style="display: none" id="stunDescription">
                                <p>
                                    Sample value:
                                    {
                                        'iceServers': [{
                                            'urls': 'turn:192.168.168.100:3478',
                                            'username': 'username',
                                            'credential': 'password'
                                        }]
                                    }
                                </p>
                                <p style="display:none">
                                    Sample twilio value:
                                    {
                                        'api':{
                                            'url': 'https://api.twilio.com/2010-04-01/Accounts/xxxx/Tokens.json',
                                            'sid': 'xxxx',
                                            'token': 'xxxx'
                                        }
                                    }
                                </p>
                                <p style="display:none">
                                    Sample xirsys value:
                                    {
                                        'api':{
                                            'url': 'https://global.xirsys.net/_turn/app_name',
                                            'sid': 'xxxx',
                                            'token': 'xxxx'
                                        }
                                    }
                                </p>
                                <br/>
                                <p>A STUN server is used to get an external network address.</p>
                                <p>STUN: STUN servers live on the public internet and have one simple task: check the IP:port address of an incoming request (from an application running behind a NAT) and send that address back as a response. In other words, the application uses a STUN server to discover its IP:port from a public perspective. This process enables a WebRTC peer to get a publicly accessible address for itself, and then pass that on to another peer via a signaling mechanism, in order to set up a direct link. (In practice, different NATs work in different ways, and there may be multiple NAT layers, but the principle is still the same.)</p>
                                <p>TURN servers are used to relay traffic if direct (peer to peer) connection fails.</p>
                                <p>TURN RTCPeerConnection tries to set up direct communication between peers over UDP. If that fails, RTCPeerConnection resorts to TCP. If that fails, TURN servers can be used as a fallback, relaying data between endpoints. Just to reiterate: TURN is used to relay audio/video/data streaming between peers, not signaling data!</p>
                                <p>TURN servers have public addresses, so they can be contacted by peers even if the peers are behind firewalls or proxies. TURN servers have a conceptually simple task — to relay a stream — but, unlike STUN servers, they inherently consume a lot of bandwidth. In other words, TURN servers need to be beefier.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    jQuery(document).ready(function(){
        jQuery('.form-group .col-md-7').append(jQuery('#stunDescription').html());
    });
    
    $(document).on('submit', '.intergration-setting', function(e) {
        var content = jQuery("textarea").val();
        if(content != ""){
            content = content.replace(/'/g, '"');
            try {
                $.parseJSON(content);
            }
            catch (err) {
                e.preventDefault();
                alert("<?php echo __d('chat','Invalid STUN/TURN server format');?>");
            }
        }
    });
<?php $this->Html->scriptEnd(); ?>