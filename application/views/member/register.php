 <div class="reg-form-box">  

<div class="header-box">
    <h1 class="main-title">Get Started with Hybrid Fitness!</h1>
    <h2 class="sub-title">Account Info</h2>
    <p class="description" >You're ready to get started with your workout goals, but first let's take care of the basics.  Don't worry, all your information is private.</p>
</div>
<?php echo form_open("member/register",'id="register_form"');?>
    <?php echo form_input($hpot);?> 
<div class="form-field">
    <div class="row">
        <div class="col-md-12">
            <div class="error"><?php echo $message;?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">Member Type:</label>
                <?php echo form_dropdown('member_type',array('members' => 'General Member','trainers' => 'Trainer'),'');?>
            </div>
        </div>
        <!-- col-closed -->
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">First Name:</label>
                <?php echo form_input($first_name);?>
            </div>
        </div>
        <!-- col-closed -->
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">Last Name:</label>
                <?php echo form_input($last_name);?>
            </div>
        </div>
        <!-- col-closed -->
    </div>
    <!-- row closed -->


    <div class="row">
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">City:</label>
                <?php echo form_input($city);?>
            </div>
        </div>
        <!-- col-closed -->
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">State:</label>
                <? echo form_dropdown('state',$state_options,$state_value,'class="required"'); ?>
            </div>
        </div>
        <!-- col-closed -->
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">Zip:</label>
                <?php echo form_input($zip);?>
            </div>
        </div>
        <!-- col-closed -->
    </div>
    <!-- row closed -->




    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">Email:</label>
                <?php echo form_input($email);?>
            </div>
        </div>
        <!-- col-closed -->
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">Username:</label>
                <?php echo form_input($username);?>
                <p class="small_print">Must be 6-15 alphanumeric characters and can use the underscore (_)</p>
            </div>
        </div>
        <!-- col-closed -->
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">Password:</label>
                <?php echo form_input($password);?>
                <p class="small_print">Must be 6-15 alphanumeric characters and can use following (_ # @ *)</p>
            </div>
        </div>
        <!-- col-closed -->

        <!-- col-closed -->
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">Confirm Password:</label>
                <?php echo form_input($password_confirm);?>
            </div>
        </div>
        <!-- col-closed -->
    </div>
    <!-- row closed -->

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label">Terms of Use:</label>
                <textarea readonly="readonly" row="20" col="12">
USER AGREEMENT: TERMS AND CONDITIONS FOR USING HYBRID FITNESS TRAINING EVOLVED ("HYBRID FITNESS" OR "HF") WEBSITE

By accessing or using HF, you (the user or "You") agree to be bound by the terms and conditions of this online services agreement ("Agreement"). This is a legally binding agreement between You and HF.  This Agreement governs your use of the HF website. If You do not agree to the terms and conditions set forth below, Your sole remedy is stop using HF, its website, and services subject to the terms and conditions of cancellation and termination below.

1.	Reservation of Rights
HF RESERVES THE RIGHT TO CHANGE THESE TERMS AND CONDITIONS OF THIS AGREEMENT AT ANY TIME.  YOU AGREE TO BE BOUND TO ANY CHANGES TO THIS AGREEMENT WHEN YOU USE HF AFTER ANY SUCH MODIFICATION IS POSTED. YOU SHOULD REVIEW THIS AGREEMENT REGULARLY TO ENSURE YOU ARE UPDATED AS TO ANY CHANGES.  IF YOU DO NOT AGREE TO ANY SUCH CHANGED TERMS AND CONDITIONS OF THIS AGREEMENT, YOUR SOLE REMEDY IS TO STOP YOUR USE OF HF'S SITE AND SERVICES, SUBJECT TO THE TERMS AND CONDITIONS OF TERMINATION AND CANCELLATION BELOW.  HF MAKES NO COMMITMENT TO UPDATE ANY INFORMATION ON ITS WEBSITE WHICH IS OFFERED SUBJECT TO THE TERMS AND CONDITIONS OF THIS AGREEMENT'S WARRANTY AND CONDITIONS BELOW.

(a)	HF Content and Intellectual Property
HF reserves the exclusive right to all intellectual property, including copyright, service and trade mark of all content provided by HF ("HF Content") on the website.  All HF Content is owned or controlled by the HF and is protected by worldwide copyright laws. You may download content only for your personal use for non-commercial purposes, but no further reproduction or modification of the content is permitted. The products, technology or processes described in this site may be the subject of other intellectual property rights reserved by HF or other third parties. No license is granted with respect to those intellectual property rights. HF Content presented here has been compiled by HF from internal and external sources. However, no representation is made or warranty given as to the completeness or accuracy of such HF Content. In particular, You should be aware that this information may be incomplete, may contain errors or may have become out of date. You should therefore verify information obtained from this site before You act upon it. HF shall not be liable for any damages or injury resulting from your access to, or inability to access this Internet site, or from your reliance on any information provided in the site.

(b)	User Content
HF has created a "Social Network" on the HF website.  It is HF's express purpose and intent, and it is the express meaning of this Agreement, to create an interactive computer service to provide access to HF's Social Network to its users and to encourage its users to communicate, network, and freely exchange information relating to fitness, health, training, diet, competition, and other common athletic pursuits and interests of its users.  Participation on the Social Network is voluntary.  Communications with other members is voluntary.  You may create a profile to participate in the Social Network.  Please choose carefully the information you post on HF and that you provide to other users. Your HF profile may not include the following items: telephone numbers, street addresses, last names, and any photographs containing nudity, or obscene, lewd, excessively violent, harassing, sexually explicit or otherwise objectionable subject matter. Despite this prohibition, information provided by other HF users may contain prohibited material and HF assumes no responsibility or liability for this material. If you become aware of misuse of the HF by any person, please report the inappropriate content to HF.

HF does not claim any ownership rights in the text, files, images, photos, video, sounds, musical works, works of authorship, or any other materials (collectively, "User Content") that You post on HF's social network.  By posting User Content on or through HF's social network, you hereby grant to HF a limited license to use, modify, publicly perform, publicly display, reproduce, and distribute such Content solely on and through HF.

You are solely responsible for the User Content that you post HF's social network, User Content that You transmit to other members, and for Your interactions with other users.  HF does not control and is not responsible for User Content that other members post on HF's social network, Content that other members transmit to You, or communications from other members.
If You post User Content on the website, or otherwise make (or allow any third party to make) material available by means of the Social Network to other users, You are entirely responsible for the content of, and any harm resulting from, that Content. That is the case regardless of whether the Content in question constitutes text, graphics, an audio file, or computer software. By making Content available, You represent and warrant that: (i) the downloading, copying and use of the Content will not infringe the proprietary rights, including but not limited to the copyright, patent, trademark or trade secret rights, of any third party; (ii) if your employer has rights to intellectual property You create, You have either (a) received permission from your employer to post or make available the Content, including but not limited to any software, or (b) secured from your employer a waiver as to all rights in or to the Content;  (iii) You have fully complied with any third-party licenses relating to the Content, and have done all things necessary to successfully pass through to end users any required terms; (iv) the Content does not contain or install any viruses, worms, malware, Trojan horses or other harmful or destructive content; (v) the Content is not spam, and does not contain unethical or unwanted commercial content designed to drive traffic to third party sites or boost the search engine rankings of third party sites, or to further unlawful acts (such as phishing) or mislead recipients as to the source of the material (such as spoofing); (vi) the Content is not obscene, libelous or defamatory (more info on what that means), hateful or racially or ethnically objectionable, and does not violate the privacy or publicity rights of any third party; and (vii) You have, in the case of Content that includes computer code, accurately categorized and/or described the type, nature, uses and effects of the materials, whether requested to do so by HF or otherwise. 
By submitting content to HF for inclusion in the forum You: (i) grant HF a world-wide, royalty-free, and non-exclusive license to reproduce, modify, adapt and publish the content solely for the purpose of displaying, distributing and promoting our service; and (ii) forever waive any right, claim, recourse, remedy, or cause of action against HF relating to the use of misuse of any such content. If You delete content, HF will use reasonable efforts to remove it from the website, but You acknowledge that caching or references to the content may not be made immediately unavailable.
Subject to the foregoing disclaimer and reservation of rights with respect User Content, HF reserves the right to protect its members, users, and the online community.  HF cannot review all User Content and users are encourages to report inappropriate User Content and communications.  HF may restrict, delete, deny access to, or remove User Content that it deems to be obscene, lewd, lascivious, filthy, excessively violent, harassing, or otherwise objectionable, whether or not You believe such material is "constitutionally" protected.  HF may terminate accounts belonging, and block access, to users that provide inappropriate User Content.

(c)	User Communications with HF
HF offers users the ability to communicate with HF to assist users with their fitness goals.  All user communications with HF shall be considered confidential or private communications.  HF will not share, publish, or otherwise post any information contained in such a user communication without Your express permission.  If You post, share, or publish any such communication, on HF, it becomes User Content; if You post, share, or publish any such communication on any forum, including HF, You waive the confidential protection under this section and release HF from any such claims.

2.	USER REPRESENTATIONS AND INDEMNIFICATION
YOU REPRESENT AND WARRANT THAT YOU ARE AT LEAST 18 YEARS OF AGE.  YOU HAVE THE LEGAL RIGHT AND ABILITY TO ENTER INTO THIS AGREEMENT AND TO USE HF IN ACCORDANCE WITH THIS AGREEMENT. YOU AGREE TO BE FINANCIALLY RESPONSIBLE FOR YOUR USE OF HF (AS WELL AS FOR USE OF YOUR ACCOUNT BY OTHERS, INCLUDING MINORS LIVING WITH YOU WITH OR WITHOUT YOUR PERMISSION) AND TO COMPLY WITH YOUR RESPONSIBILITIES AND OBLIGATIONS AS STATED IN THIS AGREEMENT.

You represent and warrant that (i) your use of the website will be in strict accordance with the HF Privacy Policy, with this Agreement and with all applicable laws and regulations (including without limitation any local laws or regulations in your country, state, city, or other governmental area, regarding online conduct and acceptable content, and including all applicable laws regarding the transmission of technical data exported from the United States or the country in which You reside) and (ii) your use of the website will not infringe or misappropriate the intellectual property rights of any third party.

If You create any account on HF's website, You are responsible for maintaining the security of your account, and You are responsible for all activities that occur under the account and any other actions taken in connection with the account. You must immediately notify HF of any unauthorized uses of your account or any other breaches of security. HF will not be liable for any acts or omissions by You, including any damages of any kind incurred as a result of such acts or omissions.

Members that are Parents or other legal guardians may create sub-user accounts for their children or charges subject to the Terms and Conditions of such sub-use.  Members sharing a common residence with minor children may not create sub-user profiles and may not allow children or other third-parties to access their user account.  Under no circumstances is HF under an obligation to verify the age or identify of a person using a member's account.  Parents, guardians, and adults sharing a common residence with minor children may obtain third-party applications providing parent control protections to adult users at service providers such as:  Cyber Patrol and Net Nanny (currently http://www.cyberpatrol.com/	and http://www.netnanny.com/, respectively, if these links are no longer active, please contact HF for assistance in locating current information).

You agree to indemnify and hold harmless HF, its contractors, licensors, employees, and agents from and against any and all claims and expenses, including attorneys' fees, arising out of your violation of this agreement.  You agree to indemnify and hold harmless HF, its contractors, employees, and agents, from all damage, claims, and expenses arising out of the use or misuse of your account.

3.	WARRANTY AND CONDITIONS
THIS PUBLICATION IS PROVIDED "AS IS," WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT.  YOUR VOLUNTARY USE OF INFORMATION ON HF SHALL NOT CREATE ANY RIGHT, CLAIM, REMEDY, OR CAUSE OF ACTION AGAINST HF.

All HF Content on the website, including HF Content available to the public, premium HF Content, and communications from HF to users relating to premiums services, is provided for informational purposes only.  HF is not and cannot supervise or teach You how to use, train, or perform any of the exercises, information, or ideas herein.  You acknowledge these terms and are responsible for obtaining proper instruction and supervision prior to and during Your use of any HF Content found on this website.  Before You begin any exercise program, and before You follow any of the advice, instructions, or any other recommendations in this website, You should first consult with your doctor and have a physical examination. The recommendations, instructions and advice contained within this website and email, are in no way intended to replace or to be construed as medical advice and/or proper supervision and instruction.  Use of the programs, advice, and information contained in this website is at Your sole choice and risk.

HF offers no warranties or representations regarding the advice, instructions, E mail advice or any other information contained in this website. 

HF and their respective agents, heirs, assigns, contractors, and employees shall not be liable for any claims, demands, damages, rights of action or causes of action, present or future, arising out of or connected to the use of any of the information contained in this website, including any injuries resulting therefrom.  You hereby forever release HF from, and waive as against HF, all claims, causes of action, rights, remedies, known or unknown, extant or potential, relating to Your use of any information found on HF's website.

This Internet site may provide links or reference to other sites.  HF has no responsibility for the content of such other sites and shall not be liable for any damages or injury arising from that content. Any links to other sites are provided merely as a convenience to the users of this Internet site.  HF has not reviewed, and cannot review, all of the material, including computer software, posted to the website, and cannot therefore be responsible for that material's content, use or effects. HF does not represent or imply that it endorses the material there posted, or that it believes such material to be accurate, useful or non-harmful. You are responsible for taking precautions as necessary to protect yourself and your computer systems from viruses, worms, Trojan horses, and other harmful or destructive content. The website may contain content that is offensive, indecent, or otherwise objectionable, as well as content containing technical inaccuracies, typographical mistakes, and other errors. The website may also contain material that violates the privacy or publicity rights, or infringes the intellectual property and other proprietary rights, of third parties, or the downloading, copying or use of which is subject to additional terms and conditions, stated or unstated. HF disclaims any responsibility for any harm resulting from the use by visitors of the website, or from any downloading by those visitors of content there posted.

HF has not reviewed, and cannot review, all of the material, including computer software, made available through the websites and webpages to which HF links, and that link to HF.  HF does not have any control over those non- HF websites, and is not responsible for their contents or their use. By linking to a non- HF website or webpage, HF does not represent or imply that it endorses such website or webpage. You are responsible for taking precautions as necessary to protect yourself and your computer systems from viruses, worms, Trojan horses, and other harmful or destructive content. HF disclaims any responsibility for any harm resulting from your use of non- HF websites and webpages.

You understand and agree that HF shall not be liable for any direct, indirect, incidental, special, consequential or exemplary damages, including but not limited to, damages for loss of profits, goodwill, use, data or other intangible losses (even if advised of the possibility of such damages), resulting from: (i) the use or the inability to use the sites or services; (ii) the cost of procurement of substitute services resulting from any data, information or services obtained or messages received or transactions entered into on the sites or through or from the service; (iii) unauthorized access to or alteration of your transmissions or data; (iv) statements or conduct of any third party on the service; or (v) any other matter relating to the sites or the services.

4.	PRIVACY POLICY
If You choose to give us personal information, such as your name, address or e-mail address, this information may be used for HF marketing and promotional material, such as catalog mailings, service and product announcements, special offers.  HF does not rent, sell, or distribute this information to other companies.

HF will allow You to voluntarily opt out of its promotional and marketing efforts and has established procedures for removing your name and address from any through on-line opt out functions.  You may also opt out at any time by email or letter to which HF will respond in reasonable time.

5.	FEES, PAYMENT, CANCELLATION, AND TERMINATION

Optional premium paid membership services are available to You on HF.  By selecting a premium service You agree to pay HF the monthly or annual subscription fees that You selected for that service. Payments will be charged on the day You sign up for a premium service and will cover the use of that service for a monthly or annual period as indicated.

If You enroll in a premium membership account, after the initial membership term concludes your membership will automatically renew to the same duration of the initial membership term and continue to renew thereafter at the end of each term. You are entitled to permanently cancel these renewals at any time.  Cancellation will not become effective until the end of the membership in which the cancellation takes place. HF will not retroactively or pro-rate refunds for any portion of a membership term during which You elect to cancel your membership.  You remain financially responsible for the membership fees until we receive notice of cancellation.

HF may terminate your access to all or any part of the website at any time, with or without cause, with or without notice, effective immediately. If You wish to terminate your non-premium membership account, You may simply discontinue using the website provided however that You will remain responsible for any liability caused by any Content You provided even if You abandon the account.  HF reserves the right to delete Your non-premium account for non-use after a reasonable period of no activity.

HF can terminate the website at any time as part of a general shut down of our service. All provisions of this agreement which by their nature should survive termination shall survive termination, including, without limitation, ownership provisions, warranty disclaimers, indemnity, limitations of liability, and the ADR (alternative dispute resolution) provision.

As a condition of your use of HF, You warrant to HF that You will not use its website(s) or services for any purpose that is unlawful or prohibited by these terms. If You violate any of these terms, your permission to use the HF's website(s) automatically terminates and your accounts will be cancelled.  You will remain responsible for any charges incurred during your premium membership account and will not be entitled to any refund for any portion of your membership term.  You will remain responsible for any liability caused by any Content You provided even if You abandon a premium membership account.  HF is not obligated to verify your use of premium membership services and You will continue to incur fees pursuant to the terms of this Agreement.

You may not, without the prior written permission of HF, use any computer code, data mining software, "robot," "bot," "spider," "scraper" or other automatic device, or program, algorithm or methodology having similar processes or functionality, or any manual process, to monitor or copy any of the webpages, data or content found on the website(s) or accessed through these website(s).

6.	ALTERNATIVE DISPUTE RESOLUTION AND OTHER TERMS

(a)	Choice of Law, Venue, Agreement to Mediate

This Agreement constitutes the entire agreement between HF and You concerning the subject matter hereof may only be modified by a written amendment signed by an authorized executive of HF, or by HF posting revised version.  This Agreement, any access to, or use of the website, will be governed by the laws of the state of New York.  You acknowledge that HF is a New York business and cannot be summoned to any court or tribunal outside of Westchester County, New York except for mediation under this Agreement.  You acknowledge that all disputes shall be venued in the state and federal courts of Westchester County, New York.  Except where otherwise required by statute, any dispute arising under this Agreement shall be subject to non-binding mediation.  Provide however, that if emergency or injunctive relief is deemed necessary, HF reserves its right to seek such relief. 

(b)	Mediation
Except as provided herein, no civil action with respect to any dispute, claim or controversy arising out of or relating to this Agreement may be commenced until the matter has been submitted to JAMS for mediation in New York City. Either party may commence mediation by providing to JAMS and the other party a written request for mediation, setting forth the subject of the dispute and the relief requested. The parties will cooperate with JAMS and with one another in selecting a mediator from JAMS panel of neutrals, and in scheduling the mediation proceedings. The parties covenant that they will participate in the mediation in good faith, and that they will share equally in its costs. All offers, promises, conduct and statements, whether oral or written, made in the course of the mediation by any of the parties, their agents, employees, experts and attorneys, and by the mediator and any JAMS employees, are confidential, privileged and inadmissible for any purpose, including impeachment, in any litigation or other proceeding involving the parties, provided that evidence that is otherwise admissible or discoverable shall not be rendered inadmissible or non-discoverable as a result of its use in the mediation. Either party may seek equitable relief prior to the mediation to preserve the status quo pending the completion of that process. Except for such an action to obtain equitable relief, neither party may commence a civil action with respect to the matters submitted to mediation until after the completion of the initial mediation session, or 45 days after the date of filing the written request for mediation, whichever occurs first. Mediation may continue after the commencement of a civil action, if the parties so desire. The provisions of this Clause may be enforced by any Court of competent jurisdiction, and the party seeking enforcement shall be entitled to an award of all costs, fees and expenses, including attorneys' fees, to be paid by the party against whom enforcement is ordered.

(c)	Severance, Non Waiver, Assignment, and Merger
If any part of this Agreement is held invalid or unenforceable, that part will be construed to reflect the parties' original intent, and the remaining portions will remain in full force and effect. A waiver by either party of any term or condition of this Agreement or any breach thereof, in any one instance, will not waive such term or condition or any subsequent breach thereof. You may assign your rights under this Agreement to any party that consents to, and agrees to be bound by, its terms and conditions; HF may assign its rights under this Agreement without condition. This Agreement will be binding upon and will inure to the benefit of the parties, their successors and permitted assigns.  This Agreement constitutes the entire Agreement between the parties and cannot be changed orally.

        </textarea>
            </div>
        </div>
    </div>
    <!-- row closed -->

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <label for="staticEmail2" class="form-label" style=" display: flex;"><?php echo form_checkbox($terms_accept);?> <span class="text-accept">Accept Terms and Conditions of Use?</span></label>
            </div>
        </div>
        <!-- col-closed -->
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <input type="submit" class="submit" value="Complete">
            </div>
        </div>
        <!-- col-closed -->
    </div>
    <!-- row closed -->

</div>
<?php echo form_close();?>




</div>