<? global $fnT; ?>
<!-- Modal -->
<div class="modal fade" id="modalEditLocation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile">
                    <div class="tile-body">
                        <form id="form-location" onsubmit="sendLocation(this); return false;">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="input-number"><?= $fnT('Number') ?>*</label>
                                    <input type="text" class="form-control" id="input-number" name="number" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="input-name"><?= $fnT('Name') ?>*</label>
                                    <input type="text" class="form-control" id="input-name" name="name" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="input-phone"><?= $fnT('Phone number') ?></label>
                                    <input type="text" class="form-control" id="input-phone" name="phone">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="input-status"><?= $fnT('Status') ?>*</label>
                                    <select class="form-control" id="input-status" name="status" required>
                                        <option value="1"><?= $fnT('Open') ?></option>
                                        <option value="0"><?= $fnT('Closed') ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="input-status"><?= $fnT('Shop type') ?>*</label>
                                    <select class="form-control" id="input-shop_type" name="shop_type" required>
                                        <option value="Franchise"><?= $fnT('Franchise') ?></option>
                                        <option value="Corporative"><?= $fnT('Corporative') ?></option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="input-email"><?= $fnT('Email') ?>*</label>
                                    <input type="text" class="form-control" id="input-email" name="email" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="input-country"><?= $fnT('Country') ?>*</label>
                                    <select class="form-control" id="input-country" name="country" required>
                                        <? foreach($data['countries'] as $country): ?>
                                            <option value="<?= $country ?>"><?= $country ?></option>
                                        <? endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="input-city"><?= $fnT('City') ?></label>
                                    <input type="text" class="form-control" id="input-city" name="city">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="input-address"><?= $fnT('Address') ?>*</label>
                                <input type="text" class="form-control" id="input-address" name="address_1" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="input-statecode"><?= $fnT('State code') ?></label>
                                    <input type="text" class="form-control" id="input-statecode" name="state_code">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="input-statename"><?= $fnT('State name') ?></label>
                                    <input type="text" class="form-control" id="input-statename" name="state_name">
                                </div>
                            </div>

                            <!--<div class="form-row border border-dark bg-light rounded p-2 my-3">
                                <div class="form-group col-md-12">
                                    <label for="input-gm">Emails: Shop / GM*</label>
                                    <input type="email" multiple class="form-control" id="input-gm" name="emails_gm" required>
                                </div>
                            </div>-->
                        </form>
                    </div>
                    <div class="tile-footer">
                        <button class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i><?=$fnT('Closed')?></button>
                        <button onclick="sendLocation(1);" class="btn btn-primary"><?=$fnT('Save changes')?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>\