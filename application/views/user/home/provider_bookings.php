<div class="content">
    <div class="container">
        <div class="row">

            <?php $this->load->view('user/home/provider_sidemenu'); ?>
            <div class="col-xl-9 col-md-8" >
                <div class="row align-items-center mb-4">
                    <div class="col">
                        <h4 class="widget-title mb-0"><?php echo (!empty($user_language[$user_selected]['lg_Booking_List'])) ? $user_language[$user_selected]['lg_Booking_List'] : $default_language['en']['lg_Booking_List']; ?></h4>
                    </div>
                    <div class="col-auto">
                        <div class="sort-by">
                            <select class="form-control-sm custom-select searchFilter" id="status">
                                <option value=''><?php echo (!empty($user_language[$user_selected]['lg_All'])) ? $user_language[$user_selected]['lg_All'] : $default_language['en']['lg_All']; ?></option>
                                <option value="1"><?php echo (!empty($user_language[$user_selected]['lg_Pending'])) ? $user_language[$user_selected]['lg_Pending'] : $default_language['en']['lg_Pending']; ?></option>
                                <option value="2"><?php echo (!empty($user_language[$user_selected]['lg_Inprogress'])) ? $user_language[$user_selected]['lg_Inprogress'] : $default_language['en']['lg_Inprogress']; ?></option>
                                <option value="3"><?php echo (!empty($user_language[$user_selected]['lg_Complete_Request'])) ? $user_language[$user_selected]['lg_Complete_Request'] : $default_language['en']['lg_Complete_Request']; ?></option>
                                <option value="5"><?php echo (!empty($user_language[$user_selected]['lg_Rejected'])) ? $user_language[$user_selected]['lg_Rejected'] : $default_language['en']['lg_Rejected']; ?>   </option>
                                <option value="7"><?php echo (!empty($user_language[$user_selected]['lg_Cancelled'])) ? $user_language[$user_selected]['lg_Cancelled'] : $default_language['en']['lg_Cancelled']; ?></option>
                                <option value="6"><?php echo (!empty($user_language[$user_selected]['lg_Completed'])) ? $user_language[$user_selected]['lg_Completed'] : $default_language['en']['lg_Completed']; ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="dataList">



                    <?php
                    if (!empty($all_bookings)) {
                        foreach ($all_bookings as $bookings) {
                            $this->db->select("service_image");
                            $this->db->from('services_image');
                            $this->db->where("service_id", $bookings['service_id']);
                            $this->db->where("status", 1);
                            $image = $this->db->get()->result_array();
                            $serv_image = array();
                            foreach ($image as $key => $i) {
                                $serv_image[] = $i['service_image'];
                            }
                            ?>

                            <div class="bookings">
                                <div class="booking-list">
                                    <div class="booking-widget">
                                        <a href="<?php echo base_url() . 'service-preview/' . str_replace(' ', '-', $bookings['service_title']) . '?sid=' . md5($bookings['service_id']); ?>" class="booking-img">
                                            <img src="<?php echo base_url() . $serv_image[0] ?>" alt="User Image">
                                        </a>
                                        <div class="booking-det-info">

                                            <?php
                                            $badge = '';
                                            $class = '';
                                            if ($bookings['status'] == 1) {
                                                $badge = 'Pending';
                                                $class = 'bg-warning';
                                            }
                                            if ($bookings['status'] == 2) {
                                                $badge = 'Inprogress';
                                                $class = 'bg-primary';
                                            }
                                            if ($bookings['status'] == 3) {
                                                $badge = 'Complete Request sent to User';
                                                $class = 'bg-success';
                                            }
                                            if ($bookings['status'] == 4) {
                                                $badge = 'Accepted';
                                                $class = 'bg-success';
                                            }
                                            if ($bookings['status'] == 5) {
                                                $badge = 'Rejected by User';
                                                $class = 'bg-danger';
                                            }
                                            if ($bookings['status'] == 6) {
                                                $badge = 'Completed Accepted';
                                                $class = 'bg-success';
                                            }
                                            if ($bookings['status'] == 7) {
                                                $badge = 'Cancelled by Provider';
                                                $class = 'bg-danger';
                                            }
                                            ?>
                                            <h3>
                                                <a href="<?php echo base_url() . 'service-preview/' . str_replace(' ', '-', $bookings['service_title']) . '?sid=' . md5($bookings['service_id']); ?>">
                                                    <?php echo $bookings['service_title'] ?>
                                                </a>
                                            </h3>
                                            <?php
                                            if (!empty($bookings['user_id'])) {
                                                $user_info = $this->db->select('*')->
                                                                from('users')->
                                                                where('id', (int) $bookings['user_id'])->
                                                                get()->row_array();
                                            }

                                            if (!empty($user_info['profile_img'])) {
                                                $image = base_url() . $user_info['profile_img'];
                                            } else {
                                                $image = base_url() . 'assets/img/user.jpg';
                                            }

                                            $user_currency_code = '';
                                            $userId = $this->session->userdata('id');
                                            If (!empty($userId)) {
                                                $service_amount = $bookings['amount'];
                                                $type = $this->session->userdata('usertype');
                                                if ($type == 'user') {
                                                    $user_currency = get_user_currency();
                                                } else if ($type == 'provider') {
                                                    $user_currency = get_provider_currency();
                                                }
                                                $user_currency_code = $user_currency['user_currency_code'];

                                                $service_amount = get_gigs_currency($bookings['amount'], $bookings['currency_code'], $user_currency_code);
                                            } else {
                                                $user_currency_code = settings('currency');
                                                $service_amount = $bookings['amount'];
                                            }
                                            ?>
                                            <ul class="booking-details">
                                                <li>
                                                    <span><?php echo (!empty($user_language[$user_selected]['lg_Booking_Date'])) ? $user_language[$user_selected]['lg_Booking_Date'] : $default_language['en']['lg_Booking_Date']; ?></span> <?= date('d M Y', strtotime($bookings['service_date'])); ?> 
                                                    <span class='badge badge-pill badge-prof <?php echo $class; ?>'><?= $badge; ?></span>
                                                </li>
                                                <li><span><?php echo (!empty($user_language[$user_selected]['lg_Booking_time'])) ? $user_language[$user_selected]['lg_Booking_time'] : $default_language['en']['lg_Booking_time']; ?></span> <?= $bookings['from_time'] ?> - <?= $bookings['to_time'] ?></li>
                                                <li><span><?php echo (!empty($user_language[$user_selected]['lg_Amount'])) ? $user_language[$user_selected]['lg_Amount'] : $default_language['en']['lg_Amount']; ?></span> <?php echo currency_conversion($user_currency_code) . $service_amount; ?></li>
                                                <li><span><?php echo (!empty($user_language[$user_selected]['lg_Location'])) ? $user_language[$user_selected]['lg_Location'] : $default_language['en']['lg_Location']; ?></span> <?php echo $bookings['location'] ?></li>
                                                <li><span><?php echo (!empty($user_language[$user_selected]['lg_Phone'])) ? $user_language[$user_selected]['lg_Phone'] : $default_language['en']['lg_Phone']; ?></span> <?php echo $user_info['mobileno'] ?></li>
                                                <li>
                                                    <span><?php echo (!empty($user_language[$user_selected]['lg_User'])) ? $user_language[$user_selected]['lg_User'] : $default_language['en']['lg_User']; ?></span>
                                                    <div class="avatar avatar-xs mr-1">
                                                        <img class="avatar-img rounded-circle" alt="User Image" src="<?php echo $image; ?>">
                                                    </div> <?= !empty($user_info['name']) ? $user_info['name'] : '-'; ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="booking-action">

                                        <?php if ($bookings['status'] == 2) {
                                            $pending = 0; ?>
                                            <a href="<?php echo base_url() ?>user-chat/booking-new-chat?book_id=<?php echo $bookings['id'] ?>" class="btn btn-sm bg-info-light">
                                                <i class="far fa-eye"></i> Chat
                                            </a> 
                                            <a href="javascript:;" class="btn btn-sm bg-danger-light myCancel" data-toggle="modal" data-target="#myCancel" data-id="<?php echo $bookings['id'] ?>" data-providerid="<?php echo $bookings['provider_id'] ?>" data-userid="<?php echo $bookings['user_id'] ?>" data-serviceid="<?php echo $bookings['service_id'] ?>"> 	
                                                <i class="fas fa-times"></i> <?php echo (!empty($user_language[$user_selected]['lg_cancel_service'])) ? $user_language[$user_selected]['lg_cancel_service'] : $default_language['en']['lg_cancel_service']; ?>
                                            </a>	

                                            <a href="javascript:;" class="btn btn-sm bg-success-light update_pro_booking_status"  data-id="<?= $bookings['id']; ?>" data-status="3" data-rowid="<?= $pending; ?>" data-review="2">
                                                <i class="fas fa-check"></i> <?php echo (!empty($user_language[$user_selected]['lg_complete_res_user'])) ? $user_language[$user_selected]['lg_complete_res_user'] : $default_language['en']['lg_complete_res_user']; ?>
                                            </a>
                                        <?php } elseif ($bookings['status'] == 1) {
                                            $pending = $bookings['status'];
                                            ?>
                                            <a href="javascript:;" class="btn btn-sm bg-success-light update_pro_booking_status"  data-id="<?= $bookings['id']; ?>" data-status="2" data-rowid="<?= $pending; ?>" data-review="2" >
                                                <i class="fas fa-check"></i> <?php echo (!empty($user_language[$user_selected]['lg_user_res_accept'])) ? $user_language[$user_selected]['lg_user_res_accept'] : $default_language['en']['lg_user_res_accept']; ?>
                                            </a>
                                            <a href="javascript:;" class="btn btn-sm bg-danger-light myCancel" data-toggle="modal" data-target="#myCancel" data-id="<?php echo $bookings['id'] ?>" data-providerid="<?php echo $bookings['provider_id'] ?>" data-userid="<?php echo $bookings['user_id'] ?>" data-serviceid="<?php echo $bookings['service_id'] ?>"> 	
                                                <i class="fas fa-times"></i> <?php echo (!empty($user_language[$user_selected]['lg_cancel_service'])) ? $user_language[$user_selected]['lg_cancel_service'] : $default_language['en']['lg_cancel_service']; ?>
                                            </a>
                                        <?php } elseif ($bookings['status'] == 3) {
                                            $pending = 0;
                                            ?>
                                        <?php } ?>	

        <?php if ($bookings['status'] == 7 || $bookings['status'] == 5) { ?>
                                            <button type="button"   data-id="<?php echo $bookings['id'] ?>"  class="btn btn-sm bg-default-light reason_modal">
                                                <i class="fas fa-info-circle"></i> Reason
                                            </button>
                                            <input type="hidden" id="reason_<?= $bookings['id']; ?>" value="<?= $bookings['reason']; ?>">
        <?php } ?>
                                    </div>

                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <p><?php echo (!empty($user_language[$user_selected]['lg_no_record_fou'])) ? $user_language[$user_selected]['lg_no_record_fou'] : $default_language['en']['lg_no_record_fou']; ?></p>
                    <?php } ?>
                    <?php
                    echo $this->ajax_pagination->create_links();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

