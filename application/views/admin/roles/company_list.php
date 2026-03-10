<?php
   
    foreach ($details as $key => $value) {
                                    # code...
        ?>
            <tr>
                <td><input type="checkbox" name="company_select" class="radio" onclick="checkOnlyOne(this)" value="<?php echo $value["PlantID"]."-".$value["FY"];?>" / ></td>
                <td><?php echo $value["FIRMNAME"]; ?></td>
                <td><?php echo _d(substr($value["YEARFROM"],0,10)); ?></td>
                <td><?php echo _d(substr($value["YEARTO"],0,10)); ?></td>
                </tr>
    <?php
    }
     ?>