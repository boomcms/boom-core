<div id="b-imageeditor">
    <div id="b-imageeditor-toolbar">
        <?= $button('rotate-left', Lang::get('boom::buttons.rotate-left'), ['id' => 'b-imageeditor-rotate-left', 'class' => 'b-button-withtext']) ?>
        <?= $button('rotate-right', Lang::get('boom::buttons.rotate-right'), ['id' => 'b-imageeditor-rotate-right', 'class' => 'b-button-withtext']) ?>
        <?= $button('crop', Lang::get('boom::buttons.crop'), ['id' => 'b-imageeditor-crop', 'class' => 'b-button-withtext']) ?>
        
        <div class="crop-tools">
            <label class="aspect-ratio">
                <p><?= Lang::get('boom::asset.aspect-ratio') ?></p>
               
                <select>
                    <option value="">Fluid</option>
                    <option value="1">1/1</option>
                    <option value="1.33333">4/3</option>
                    <option value="0.5">1/2</option>
                    <option value="0.75">3/4</option>
                    <option value="1.77778">16/9</option>
                </select>
            </label>

            <?= $button('check', Lang::get('boom::buttons.accept-crop'), ['id' => 'b-imageeditor-crop-accept', 'class' => 'b-button-withtext']) ?>
            <?= $button('times', Lang::get('boom::buttons.cancel'), ['id' => 'b-imageeditor-crop-cancel', 'class' => 'b-button-withtext']) ?>
         </div>
        
        <?= $button('undo', Lang::get('boom::buttons.image-revert'), ['id' => 'b-imageeditor-revert', 'class' => 'b-button-withtext']) ?>
    </div>

    <img id="b-imageeditor-image" src="" />
</div>