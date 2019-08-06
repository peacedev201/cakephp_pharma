<?php
$groupHelper = MooCore::getInstance()->getHelper('Group_Group');
$topic_id = !empty( $this->request->named['topic_id'] ) ? $this->request->named['topic_id'] : 0;
$video_id = !empty( $this->request->named['video_id'] ) ? $this->request->named['video_id'] : 0;
$tab = !empty( $tab ) ? $tab : '';
?>
 


<?php foreach ($groups as $groups): 
    if($groups['Group']['id']== $cat_id){
         $category = $groups['Category']['name'];
         switch ($groups['Group']['type']) {
            case 1:
                $type = 'Public (anyone can view and join)';
                break;

            case 2:
                $type= 'Private (only group members can view details)';
                break;

            case 3:
                $type = 'Restricted (anyone can join upon approval)';
                break;
        }
        $established = date('Y.m.d',strtotime($groups['Group']['created']));
        $member = $groups['Group']['group_user_count'];
        $points = $groups['Group']['credit_3month_mem_aver'];
        $certificate = $groups['Group']['certificate'];
        $candidate = $groups['Group']['candidate_list'];
        $friend = $groups['Group']['friend_find_support'];    
    }    
endforeach; ?>

<script>
    console.log('<?php echo  $this->request->base ?>');
var id = <?php echo json_encode($cat_id, JSON_PRETTY_PRINT); ?>;
var apply_status = 0;
var sns_groups = <?php echo json_encode($sns_groups, JSON_PRETTY_PRINT); ?>;
var member = <?php echo json_encode($member, JSON_PRETTY_PRINT); ?>;
var points = <?php echo json_encode($points, JSON_PRETTY_PRINT); ?>;
var certificate = <?php echo json_encode($certificate, JSON_PRETTY_PRINT); ?>;
var candidate = <?php echo json_encode($candidate, JSON_PRETTY_PRINT); ?>;
var friend = <?php echo json_encode($friend, JSON_PRETTY_PRINT); ?>;
var d = 0;
function val() {
    apply_status = 0;
    d = document.getElementById("select_id").value;
    var selected_item = null;
    sns_groups.forEach(function(item, index){
        if(item.GroupsDefinition.community === d){
            selected_item = item.GroupsDefinition;
            $('.ave_point').text(selected_item.min_ave_points);
            $('.no_mem').text(selected_item.minimum_no);
            if(selected_item.certificate == 1)
                $('.certificate').text("Required");
            else
                $('.certificate').text("Not Required");
            if(selected_item.candidate_list == 1)
                $('.candidate').text("Required");
            else   
                $('.candidate').text("Not Required");
            if(selected_item.minimum_no < member){
                $('.check_member').html("").html("<div style = 'display: inline-block; vertical-align: middle;border-radius: 50%; background:green; width: 15px; float: right;margin-right: 30px;height:15px'></div>");
            }   
            else{
                apply_status = 1;
                $('.check_member').html("").html("<div style = 'display: inline-block; vertical-align: middle;border-radius: 50%; background:red; width: 15px; float: right;margin-right: 30px;height:15px'></div>");
            }               
            if(selected_item.min_ave_points < points){
                $('.check_points').html("").html("<div style = 'display: inline-block; vertical-align: middle;border-radius: 50%; background:green; width: 15px; float: right;margin-right: 30px;height:15px'></div>");
            }
                
            else{
                apply_status = 1;
                $('.check_points').html("").html("<div style = 'display: inline-block; vertical-align: middle;border-radius: 50%; background:red; width: 15px; float: right;margin-right: 30px;height:15px'></div>");
            }
                
            if(selected_item.certificate <= certificate){
                $('.check_certificate').html("").html("<div style = 'display: inline-block; vertical-align: middle;border-radius: 50%; background:green; width: 15px; float: right;margin-right: 30px;height:15px'></div>");
            }
                
            else{
                apply_status = 1;
                $('.check_certificate').html("").html("<div style = 'display: inline-block; vertical-align: middle;border-radius: 50%; background:red; width: 15px; float: right;margin-right: 30px;height:15px'></div>");
            }
                
            if(selected_item.candidate_list <= candidate){
                $('.check_candidate').html("").html("<div style = 'display: inline-block; vertical-align: middle;border-radius: 50%; background:green; width: 15px; float: right;margin-right: 30px;height:15px'></div>");
            }
                
            else{
                apply_status = 1;
                $('.check_candidate').html("").html("<div style = 'display: inline-block; vertical-align: middle;border-radius: 50%; background:red; width: 15px; float: right;margin-right: 30px;height:15px'></div>");
            }
                
            // if(friend != 1)
            //     apply_status = 1;    

            if (apply_status == 0){
                $('.apply_button').html("").html("<button type='button' onclick='myFunction()' style =' margin-left:70%;' class='btn btn-info btn-lg' data-toggle='modal' data-target='#submit_success'>Apply</button>");
            }
            else{
                $('.apply_button').html("").html("<button type='button' style =' margin-left:70%;' class='btn btn-info btn-lg' data-toggle='modal' data-target='#submit_error'>Apply</button>");
            } 
        }

        
    });
    
}
function myFunction() {
    jQuery.ajax({
        url: "<?php echo  $this->request->base ?>/groups/update",
        type: 'POST',
        data: {
            id: id,
            value: d
        },
    });
}

</script> 


<?php //dd($val);?>
<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php
        $display = true;
        if ($group['Group']['type'] == PRIVACY_PRIVATE) {
            if (empty($is_member)) {
                $display = false;
                if(!empty($cuser) && $cuser['Role']['is_admin'])
                    $display = true;
            }
        }
    ?>
    
    <?php if($display): ?>
    <div class="left-right-menu">
        <img src="<?php echo $groupHelper->getImage($group, array('prefix' => '300_square'))?>" class="page-avatar" id="av-img">
            <h1 class="info-home-name"><?php echo h($group['Group']['name'])?></h1>
            <div class="menu block-body menu_top_list">
            <ul class="list2" id="browse" style="margin-bottom: 10px">
                <li class="current">
                            <a class="no-ajax" href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>"><i class="material-icons">library_books</i> <?php echo __( 'Details')?></a>
                    </li>       
                    <li><a data-url="<?php echo $this->request->base?>/groups/members/<?php echo $group['Group']['id']?>" rel="profile-content" id="teams" href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>/tab:teams"><i class="material-icons">people</i>
                            <?php echo __( 'Members')?> <span id="group_user_count" class="badge_counter"><?php echo $group['Group']['group_user_count']?></span></a>
                    </li>
                    <li><a data-url="<?php echo $this->request->base?>/photos/ajax_browse/group_group/<?php echo $group['Group']['id']?>" rel="profile-content" id="photos" href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>/tab:photos"><i class="material-icons">collections</i>
                        <?php echo __('Photos')?> <span id="group_photo_count" class="badge_counter"><?php echo $group['Group']['photo_count'];?></span></a>
                    </li>
                <?php foreach ($group_menu as $item): ?>
                <li><a data-url="<?php echo $item['dataUrl']?>" rel="profile-content" id="<?php echo $item['id']?>" href="<?php echo $item['href']?>"><i class="material-icons"><?php echo $item['icon-class']?></i>
                    <?php echo $item['name']?> <span id="<?php echo $item['id_count']?>" class="badge_counter"><?php echo $item['item_count']?></span></a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    
<?php $this->end(); ?>
<?php $this->setNotEmpty('east');?>
<?php $this->start('east'); ?>	
<?php $this->end(); ?>

<div class="bar-content">
<div class="content_center">
    <h1><?php echo ( 'Community Now')?></h1>
    <ul class = "group-info info">
    <li>
        <label><?php echo ( 'Catagory:')?></label>
        <div><?php echo ( $category)?> </div>
    </li>
    <li>
        <label><?php echo ( 'Type:')?></label>
        <div><?php echo ( $type)?> </div>
    </li>
    <li>
        <label><?php echo ( 'Established:')?></label>
        <div><?php echo ( $established)?> </div>
    </li>
    <li>
        <label ><?php echo ( 'Members:')?></label>
        <div><?php echo ( $member)?> </div>
    </li>
    <li>
    <label ><?php echo ( '')?></label>
    </li>
    <li>
        <label style = "width:50%"><?php echo ( 'Community Member Activity:')?></label>
        <div><?php echo ($points)?> </div>
    </li>
    <li class="seperate"></li>
    <li>
        <label style = "width:40%"><?php echo ( 'Select Target Membership:')?></label>
        <select onchange="val()" id="select_id">
            <?php 
            foreach ($sns_groups as $sns_groups): 
            {
                $data = $sns_groups['GroupsDefinition']['community'];
                echo "<option value='$data'>" . $sns_groups['GroupsDefinition']['name'] . "</option>";
            }

            endforeach;
            ?>
        </select>
    </li>

    <li>
    <label ><?php echo ( '')?></label>
    </li>
    <li>
    <label ><?php echo ( '')?></label>
    </li>
    <li>
    <label ><?php echo ( 'Requirement:')?></label>
    </li>
    <li>
    <label ><?php echo ( '')?></label>
    </li>
    <li>
        <label class = "row" style = "width:50%;">No of Members (> <span class="no_mem">1</span>):
        <span class = "check_member">
            <div style = "display: inline-block; vertical-align: middle;border-radius: 50%; background:green; width: 15px; float: right;margin-right: 30px;height:15px"></div>
        </span>
        </label>
        <label style = "width:50%">Community Member Activity(> <span class="ave_point">0</span>):
            <span class = "check_points">
                <div style = "display: inline-block; vertical-align: middle;border-radius: 50%; background:green; width: 15px; float: right;margin-right: 30px;height:15px"></div>
            </span>
        </label>
    </li> 
    <li>
        <label style = "width:50%"> Verification: <span class="certificate">Not required</span>
            <span class = "check_certificate">
                <div style = "display: inline-block; vertical-align: middle;border-radius: 50%; background:green; width: 15px; float: right;margin-right: 30px;height:15px"></div>
            </span>
        </label>
        <label style = "width:50%">Candidate List Upload: <span class="candidate">Not required</span>
            <span class = "check_candidate">
                <div style = "display: inline-block; vertical-align: middle;border-radius: 50%; background:green; width: 15px; float: right;margin-right: 30px;height:15px"></div>
            </span>
        </label>
        <label style = "width:50%; margin-left:50%">(go to Candidate List Upload)
        </label>
    </li>
    <li>
        <label style = "width:50%">Friend find support:
        <?php if($friend == 1) { ?>
            <span class = "check_friend">
                <div style = "display: inline-block; vertical-align: middle;border-radius: 50%; background:green; width: 15px; float: right;margin-right: 30px;height:15px"></div>
            </span>
        <?php } else {?>
            <span class = "check_friend">
                <div style = "display: inline-block; vertical-align: middle;border-radius: 50%; background:red; width: 15px; float: right;margin-right: 30px;height:15px"></div>
            </span>
        <?php } ?>
    </label>
    </li> 
    </ul>
    <span class = "apply_button">
        <button type="button" onclick="myFunction()" id = "success_button" style =' margin-left:70%;' class="success_button btn btn-info btn-lg" data-toggle="modal" data-target="#submit_success">Apply</button>
    </span>

    <div class="modal fade" id="submit_success" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Congratulation!</h4>
            </div>
            <div class="modal-body">
                <p>Community Level up requests has been submitted to admin for approval.</p>
                <p>When approved, we will send message to you.</p>
                <p>Thanks.</p>
                <p style ="margin-left:70%; font-size:20px" >PharmaTalk  team</p>         

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </div>
            
        </div>
    </div>
    <div class="modal fade" id="submit_error" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Information</h4>
            </div>
            <div class="modal-body">
                <div class  = "row">
                    <span>
                        “Apply” button works only when there is no “Red circle”. (
                    <div style = "display: inline-block; vertical-align: middle;border-radius: 50%; background:red; width: 15px; height:15px"></div>
                    ) at below 4 requirement
                    </span>
                </div>
                <P></p>
                <P>(No. of members, Member Activity, Verification, Candidate List Upload)</p>
                <P></p>
                <P>Please resolve the red marked factor.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </div>
            
        </div>
    </div>
</div>
</div>




