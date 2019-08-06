<div id="myid">
	<div class="suggestion-help-popup">
		<ul>
			<li>
				<a href='javascript: toggleItem("id_outlook")'><?php echo __d('friend_inviter','Microsoft Outlook'); ?></a>
				<ul style="display:none;" id="id_outlook">
					<li>	
						<?php echo __d('friend_inviter','To export a CSV file from Microsoft Outlook:'); ?>						
						<ol>
							<li><?php echo __d('friend_inviter','1. Open Outlook'); ?></li>
							<li><?php echo __d('friend_inviter',"2. Go to File menu and select 'Import and Export'"); ?></li>
							<li><?php echo __d('friend_inviter',"3. In the wizard window that appears, select 'Export to a file' and click 'Next'"); ?></li>
							<li><?php echo __d('friend_inviter',"4. Select 'Comma separated values (Windows)' and click 'Next'"); ?></li>
							<li><?php echo __d('friend_inviter',"5. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo __d('friend_inviter',"6. Ensure that the checkbox next to 'Export..' is checked and click 'Finish'."); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>
			<li>
				<a href='javascript: toggleItem("id_microsoftoutlook")'><?php echo __d('friend_inviter','Microsoft Outlook Express'); ?></a>
				<ul style="display:none" id="id_microsoftoutlook">
					<li>
						<?php echo __d('friend_inviter','To export a CSV file from Microsoft Outlook Express:'); ?>
						
						<ol>
							<li><?php echo __d('friend_inviter','1. Open Outlook Express'); ?></li>
							<li><?php echo __d('friend_inviter',"2. Go to File menu and select 'Export', and then click 'Address Book'"); ?></li>
							<li><?php echo __d('friend_inviter',"3. Select 'Text File (Comma Separated Values)', and then click 'Export'"); ?></li>
							<li><?php echo __d('friend_inviter',"4. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo __d('friend_inviter',"5. Select the check boxes for the fields that you want to export (be sure to select the email address field), and then click 'Finish'."); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>
			<li>
		    <a href='javascript: toggleItem("id_mozila_thunder")'><?php echo __d('friend_inviter','Mozilla Thunderbird'); ?></a>
				<ul style="display:none" id="id_mozila_thunder">
					<li>
						<?php echo __d('friend_inviter','To export a CSV file from Mozilla Thunderbird:'); ?>
						
						<ol>
							<li><?php echo __d('friend_inviter','1. Open Mozilla Thunderbird'); ?></li>
							<li><?php echo __d('friend_inviter',"2. Go to Tools menu and select 'Address Book'"); ?></li>
							<li><?php echo __d('friend_inviter',"3. In the 'Address Book' window that opens, select 'Export...' from the Tools menu"); ?></li>
							<li><?php echo __d('friend_inviter',"4. Select where you want to save the exported file, choose 'Comma Separated (*.CSV)' under the 'Save as type' dropdown list, choose a name for your file (example : mycontacts.csv) and click 'Save'."); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>
			<li>
				<a href='javascript: toggleItem("id_linkedin")'><?php echo __d('friend_inviter','LinkedIn'); ?></a>
				<ul style="display:none" id="id_linkedin">
					<li>
						<?php echo __d('friend_inviter','To export a CSV file from LinkedIn:'); ?>
						
						<ol>
							<li><?php echo __d('friend_inviter','1. Sign into your LinkedIn account'); ?></li>
							<li><?php echo __d('friend_inviter','2. Visit the'); ?> <a href='http://www.linkedin.com/addressBookExport' target="_blank"><?php echo __d('friend_inviter','Address Book Export page'); ?></a></li>
							<li><?php echo __d('friend_inviter',"3. Select 'Microsoft Outlook (.CSV file)' under the 'Export to' dropdown list and click 'Export'"); ?></li>
							<li><?php echo __d('friend_inviter','4. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv).'); ?></li>
						</ol>
					</li>	
				</ul>
			</li>
			<li>
				<a href='javascript: toggleItem("id_windowabook")'><?php echo __d('friend_inviter','Windows Address Book'); ?></a>
				<ul style="display:none" id="id_windowabook">
					<li>
						<?php echo __d('friend_inviter','To export a CSV file from Windows Address Book:'); ?>
					<ol>
							<li><?php echo __d('friend_inviter','1. Open Windows Address Book'); ?></li>
							<li><?php echo __d('friend_inviter',"2. Go to the File menu, select 'Export', and then select 'Other Address Book...'"); ?></li>
							<li><?php echo __d('friend_inviter',"3. In the 'Address Book Export Tool' dialog that opens, select 'Text File (Comma Separated Values)' and click 'Export'"); ?></li>
							<li><?php echo __d('friend_inviter',"4. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo __d('friend_inviter',"5. Select the check boxes for the fields that you want to export (be sure to select the email address field), and then click 'Finish'."); ?></li>
							<li><?php echo __d('friend_inviter',"6. Click 'OK' and then click 'Close'"); ?></li>
						</ol>
					</li>	
				</ul>
			</li>	
			<li>
				<a href='javascript: toggleItem("id_macos")'><?php echo __d('friend_inviter','Mac OS X Address Book'); ?></a>
				<ul style="display:none" id="id_macos">
					<li>
					<?php echo __d('friend_inviter','To export a CSV file from Mac OS X Address Book:'); ?>
					
						<ol>
							<li><?php echo __d('friend_inviter','1. Download the free Mac Address Book exporter from'); ?> <a href='http://www.apple.com/downloads/macosx/productivity_tools/exportaddressbook.html' target="_blank"><?php echo __d('friend_inviter','here'); ?></a>.</li>
							<li><?php echo __d('friend_inviter','2. Choose to export your Address Book in CSV format.'); ?></li>
							<li><?php echo __d('friend_inviter','3. Save your exported address book in CSV format.'); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>	
			<li>
				<a href='javascript: toggleItem("id_palmdesktop")'><?php echo __d('friend_inviter','Palm Desktop'); ?></a>
				<ul style="display:none" id="id_palmdesktop">
					<li>
						<?php echo __d('friend_inviter','To export a CSV file from Palm Desktop:'); ?>
						
						<ol>
							<li><?php echo __d('friend_inviter','1. Open Palm Desktop'); ?></li>
							<li><?php echo __d('friend_inviter',"2. Click on the 'Addresses' icon on the lefthand side of the screen to display your contact list"); ?></li>
							<li><?php echo __d('friend_inviter',"3. Go to the File menu, select 'Export'"); ?></li>
							<li><?php echo __d('friend_inviter','4. In the dialog box that opens, do the following:'); ?></li>
							<li><?php echo __d('friend_inviter',"5. Enter a name for the file you are creating in the 'File name:' field"); ?></li>
							<li><?php echo __d('friend_inviter',"6. Select 'Comma Separated' in the 'Export Type' pulldown menu"); ?></li>
							<li><?php echo __d('friend_inviter',"7. Be sure to select the 'All' radio button from the two 'Range:' radio buttons"); ?></li>
							<li><?php echo __d('friend_inviter',"8. In the second dialog box: 'Specify Export Fields' that opens, leave all of the checkboxes checked, and click 'OK'."); ?></li>
						</ol>
					</li>
				</ul>
			</li>
			<li>
				<a href='javascript: toggleItem("id_windowmail")'><?php echo __d('friend_inviter','Windows Mail'); ?></a>
				<ul style="display:none" id="id_windowmail">
					<li>
						<?php echo __d('friend_inviter','To export a CSV file from Windows Mail:'); ?>
						
						<ol>
							<li><?php echo __d('friend_inviter','1. Open Windows Mail'); ?></li>
							<li><?php echo __d('friend_inviter','2. Select: Tools | Windows Contacts... from the menu in Windows Mail'); ?></li>
							<li><?php echo __d('friend_inviter',"3. Click 'Export' in the toolbar"); ?></li>
							<li><?php echo __d('friend_inviter',"4. Make sure CSV (Comma Separated Values) is highlighted, then click 'Export'"); ?></li>
							<li><?php echo __d('friend_inviter',"5. Select where you want to save the exported CSV file, choose a name for your file (example : mycontacts.csv) and click 'Next'"); ?></li>
							<li><?php echo __d('friend_inviter',"6. Click 'Save' then click 'Next'"); ?></li>
							<li><?php echo __d('friend_inviter','7. Make sure all address book fields you want included are checked'); ?></li>
							<li><?php echo __d('friend_inviter',"8. Click 'Finish'"); ?></li>
							<li><?php echo __d('friend_inviter',"9. Click 'OK' then click 'Close'"); ?></li>
						</ol>	
					</li>	
				</ul>
			</li>	
			<li>
				<a href='javascript: toggleItem("id_othermail")'><?php echo __d('friend_inviter','For Other'); ?></a>
				<ul style="display:none" id="id_othermail">
					<li>
						<?php echo __d('friend_inviter','Many email services, email applications, address book management applications allow contacts to be imported to a file. We support .CSV and .TXT types of contact files'); ?>
					</li>	
				</ul>
			</li>	
			<script type="text/javascript">
                            function toggleItem(divid){
                                $('#' + divid).toggle();
                            }
			</script>
		</ul>
		
	</div>	
    <button class="btn btn-action" data-dismiss="modal"><?php echo __d('friend_inviter','Close'); ?></button>
</div>