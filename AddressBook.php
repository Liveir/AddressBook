<?php
session_start();

if (!isset($_SESSION['addressBook'])) {
  $_SESSION['addressBook'] = array();
}

if (isset($_SESSION['editMode'])) {
    $editMode = $_SESSION['editMode'];
}
  

function addMember($name, $number, $address) {
  $member = array(
    'name' => $name,
    'number' => $number,
    'address' => $address
    );
    $index = count($_SESSION['addressBook']);
    $_SESSION['addressBook'][$index] = $member;
}

if (isset($_POST['add-member'])) {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $address = $street . ", " . $city . ", " . $state . ", " . $country;
    addMember($name, $number, $address);
}

function editMember($name, $number, $address) {
    $member = array(
        'name' => $name,
        'number' => $number,
        'address' => $address
        );
        $index = count($_SESSION['addressBook']);
        $_SESSION['addressBook'][$index] = $member;
}

if (isset($_POST['edit-member'])) {
    $index = $_POST['index'];
    $_SESSION['addressBook'][$index]['editMode'] = !$_SESSION['addressBook'][$index]['editMode'];
    
    // only update member fields if input fields have changed
    if (isset($_POST['name']) && isset($_POST['number']) && isset($_POST['address'])) {
        $name_edit = $_POST['name-edit'];
        $number_edit = $_POST['number-edit'];
        $address_edit = $_POST['address-edit'];
        if ($name_edit !== $_SESSION['addressBook'][$index]['name']) {
            $_SESSION['addressBook'][$index]['name'] = $name_edit;
        }
        if ($number_edit !== $_SESSION['addressBook'][$index]['number']) {
            $_SESSION['addressBook'][$index]['number'] = $number_edit;
        }
        if ($address_edit !== $_SESSION['addressBook'][$index]['address']) {
            $_SESSION['addressBook'][$index]['address'] = $address_edit;
        }
    }
}

if (isset($_POST['save-member'])) {
    $index = $_POST['index'];
    $_SESSION['addressBook'][$index]['editMode'] = !$_SESSION['addressBook'][$index]['editMode'];
    $name_edit = $_POST['name-edit'];
    $number_edit = $_POST['number-edit'];
    $address_edit = $_POST['address-edit'];
    if ($name_edit != $_SESSION['addressBook'][$index]['name']) {
        $_SESSION['addressBook'][$index]['name'] = $name_edit;
    }
    if ($number_edit != $_SESSION['addressBook'][$index]['number']) {
        $_SESSION['addressBook'][$index]['number'] = $number_edit;
    }
    if ($address_edit != $_SESSION['addressBook'][$index]['address']) {
        $_SESSION['addressBook'][$index]['address'] = $address_edit;
    }
}




function deleteMember($index) {
    $addressBook = $_SESSION['addressBook'];
    unset($addressBook[$index]);

    // update the keys of the remaining members
    $addressBook = array_values($addressBook);
    $_SESSION['addressBook'] = $addressBook;
}

if (isset($_POST['delete-member'])) {
    $index = $_POST['index'];
    deleteMember($index);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Book</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>
        /* form styling */
        h1 {
            text-align: center;
        }

        form {
            margin: 0 auto;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 10px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 10px;
        }

        select {
            padding: 10px;
            background-color: white;
            outline: none;
            border-radius: 5px;
            width: 100%;
            margin: 5px 0 20px 0;
            display: inline-block;
            border: none;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type=text], input[type=tel] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px 0;
            display: inline-block;
            border: none;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type=submit], button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }

        input[type=submit]:hover, button:hover {
         background-color: #45a049;
        }

        .container {
            padding: 20px;
            margin: 20px;
            background-color: #f2f2f2;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);

        }

        /* table styling */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            margin: 0;
            width: 150px;
        }

        th {    
            background-color: #4CAF50;
            color: white;
            padding-left: 13px;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .narrowest-cell {
            width: 50px;
            padding-top: 20px;
            text-align: center;
        }

        .narrower-cell {
            width: 100px;
            padding-top: 20px;
        }

        .narrow-cell {
            width: 150px;
            padding-top: 20px;
        }

        .normal-cell {
            width: 600px;
            padding-top: 20px;
        }

        .delete-cell {
            width: 125px;
        }

        .table-container {
            max-height: 400px; /* set the maximum height of the container */
            overflow-y: auto; /* enable vertical scrolling */
        }


        /* modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-wrapper,
        .btn-form {
            display: flex;
            justify-content: center; 
            margin: 1px;
            padding: 1px;   
            width:150px;  
        }

        .delete-btn,
        .edit-btn {
            color: white;
            padding: 5px 10px;
            margin: 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            justify-content: center;
            width: 70px;
        }

        .delete-btn {
            background-color: red;
        }

        .edit-btn {
            background-color: black;
        }

        .table-input {
            background-color: #f2f2f2;
            padding-bottom: 0;
            align-items: center;
            vertical-align: middle;
        }

        .table-input:disabled {
            color: black;
        }

    </style>
    <div class="container">
        <div>
            <h1>Address Book</h1>
            <form id="add-member-form" class="form" action="" method="post">
                <div class="form-control">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-control">
                    <label for="number">Phone Number</label>
                    <input type="tel" id="number" name="number" required>
                </div>
                <div class="form-control">
                    <label for="street">Street Address</label>
                    <input type="text" id="street" name="street" required>
                </div>
                <div class="form-control">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" required>
                </div>
                <div class="form-control">
                    <label for="state">State/Province</label>
                    <input type="text" id="state" name="state" required>
                </div>
                <div class="form-control">
                    <label for="country">Country</label>
                    <select id="country" name="country" required>
                        <option value=""></option>
                        <option value="Afghanistan">Afghanistan</option>
                        <option value="Albania">Albania</option>
                        <option value="Algeria">Algeria</option>
                        <option value="Andorra">Andorra</option>
                        <option value="Angola">Angola</option>
                        <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Armenia">Armenia</option>
                        <option value="Australia">Australia</option>
                        <option value="Austria">Austria</option>
                        <option value="Azerbaijan">Azerbaijan</option>
                        <option value="Bahamas">Bahamas</option>
                        <option value="Bahrain">Bahrain</option>
                        <option value="Bangladesh">Bangladesh</option>
                        <option value="Barbados">Barbados</option>
                        <option value="Belarus">Belarus</option>
                        <option value="Belgium">Belgium</option>
                        <option value="Belize">Belize</option>
                        <option value="Benin">Benin</option>
                        <option value="Bhutan">Bhutan</option>
                        <option value="Bolivia">Bolivia</option>
                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                        <option value="Botswana">Botswana</option>
                        <option value="Brazil">Brazil</option>
                        <option value="Brunei">Brunei</option>
                        <option value="Bulgaria">Bulgaria</option>
                        <option value="Burkina Faso">Burkina Faso</option>
                        <option value="Burundi">Burundi</option>
                        <option value="Cabo Verde">Cabo Verde</option>
                        <option value="Cambodia">Cambodia</option>
                        <option value="Cameroon">Cameroon</option>
                        <option value="Canada">Canada</option>
                        <option value="Central African Republic">Central African Republic</option>
                        <option value="Chad">Chad</option>
                        <option value="Chile">Chile</option>
                        <option value="China">China</option>
                        <option value="Colombia">Colombia</option>
                        <option value="Comoros">Comoros</option>
                        <option value="Congo, Democratic Republic of the">Congo, Democratic Republic of the</option>
                        <option value="Congo, Republic of the">Congo, Republic of the</option>
                        <option value="Costa Rica">Costa Rica</option>
                        <option value="Côte d'Ivoire">Côte d'Ivoire</option>
                        <option value="Croatia">Croatia</option>
                        <option value="Cuba">Cuba</option>
                        <option value="Cyprus">Cyprus</option>
                        <option value="Czech Republic">Czech Republic</option>
                        <option value="Denmark">Denmark</option>
                        <option value="Djibouti">Djibouti</option>
                        <option value="Dominica">Dominica</option>
                        <option value="Dominican Republic">Dominican Republic</option>
                        <option value="East Timor (Timor-Leste)">East Timor (Timor-Leste)</option>
                        <option value="Ecuador">Ecuador</option>
                        <option value="Egypt">Egypt</option>
                        <option value="El Salvador">El Salvador</option>
                        <option value="Equatorial Guinea">Equatorial Guinea</option>
                        <option value="Eritrea">Eritrea</option>
                        <option value="Estonia">Estonia</option>
                        <option value="Eswatini">Eswatini</option>
                        <option value="Ethiopia">Ethiopia</option>
                        <option value="Fiji">Fiji</option>
                        <option value="Finland">Finland</option>
                        <option value="France">France</option>
                        <option value="Gabon">Gabon</option>
                        <option value="Gambia">Gambia</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Germany">Germany</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Greece">Greece</option>
                        <option value="Grenada">Grenada</option>
                        <option value="Guatemala">Guatemala</option>
                        <option value="Guinea">Guinea</option>
                        <option value="Guinea-Bissau">Guinea-Bissau</option>
                        <option value="Guyana">Guyana</option>
                        <option value="Haiti">Haiti</option>
                        <option value="Honduras">Honduras</option>
                        <option value="Hungary">Hungary</option>
                        <option value="Iceland">Iceland</option>
                        <option value="India">India</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Iran">Iran</option>
                        <option value="Iraq">Iraq</option>
                        <option value="Ireland">Ireland</option>
                        <option value="Israel">Israel</option>
                        <option value="Italy">Italy</option>
                        <option value="Jamaica">Jamaica</option>
                        <option value="Japan">Japan</option>
                        <option value="Jordan">Jordan</option>
                        <option value="Kazakhstan">Kazakhstan</option>
                        <option value="Kenya">Kenya</option>
                        <option value="Kiribati">Kiribati</option>
                        <option value="Kosovo">Kosovo</option>
                        <option value="Kuwait">Kuwait</option>
                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                        <option value="Laos">Laos</option>
                        <option value="Latvia">Latvia</option>
                        <option value="Lebanon">Lebanon</option>
                        <option value="Lesotho">Lesotho</option>
                        <option value="Liberia">Liberia</option>
                        <option value="Libya">Libya</option>
                        <option value="Liechtenstein">Liechtenstein</option>
                        <option value="Lithuania">Lithuania</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="Madagascar">Madagascar</option>
                        <option value="Malawi">Malawi</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="Maldives">Maldives</option>
                        <option value="Mali">Mali</option>
                        <option value="Malta">Malta</option>
                        <option value="Marshall Islands">Marshall Islands</option>
                        <option value="Mauritania">Mauritania</option>
                        <option value="Mauritius">Mauritius</option>
                        <option value="Mexico">Mexico</option>
                        <option value="Micronesia">Micronesia</option>
                        <option value="Moldova">Moldova</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Mongolia">Mongolia</option>
                        <option value="Montenegro">Montenegro</option>
                        <option value="Morocco">Morocco</option>
                        <option value="Mozambique">Mozambique</option>
                        <option value="Myanmar (Burma)">Myanmar (Burma)</option>
                        <option value="Namibia">Namibia</option>
                        <option value="Nauru">Nauru</option>
                        <option value="Nepal">Nepal</option>
                        <option value="Netherlands">Netherlands</option>
                        <option value="New Zealand">New Zealand</option>
                        <option value="New Zealand">New Zealand</option>
                        <option value="Nicaragua">Nicaragua</option>
                        <option value="Niger">Niger</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="North Korea">North Korea</option>
                        <option value="North Macedonia">North Macedonia</option>
                        <option value="Norway">Norway</option>
                        <option value="Oman">Oman</option>
                        <option value="Pakistan">Pakistan</option>
                        <option value="Palau">Palau</option>
                        <option value="Palestine State">Palestine State</option>
                        <option value="Panama">Panama</option>
                        <option value="Papua New Guinea">Papua New Guinea</option>
                        <option value="Paraguay">Paraguay</option>
                        <option value="Peru">Peru</option>
                        <option value="Philippines">Philippines</option>
                        <option value="Poland">Poland</option>
                        <option value="Portugal">Portugal</option>
                        <option value="Qatar">Qatar</option>
                        <option value="Romania">Romania</option>
                        <option value="Russia">Russia</option>
                        <option value="Rwanda">Rwanda</option>
                        <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                        <option value="Saint Lucia">Saint Lucia</option>
                        <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                        <option value="Samoa">Samoa</option>
                        <option value="San Marino">San Marino</option>
                        <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                        <option value="Saudi Arabia">Saudi Arabia</option>
                        <option value="Senegal">Senegal</option>
                        <option value="Serbia">Serbia</option>
                        <option value="Seychelles">Seychelles</option>
                        <option value="Sierra Leone">Sierra Leone</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Slovakia">Slovakia</option>
                        <option value="Slovenia">Slovenia</option>
                        <option value="Solomon Islands">Solomon Islands</option>
                        <option value="Somalia">Somalia</option>
                        <option value="South Africa">South Africa</option>
                        <option value="South Korea">South Korea</option>
                        <option value="South Sudan">South Sudan</option>
                        <option value="Spain">Spain</option>
                        <option value="Sri Lanka">Sri Lanka</option>
                        <option value="Sudan">Sudan</option>
                        <option value="Suriname">Suriname</option>
                        <option value="Sweden">Sweden</option>
                        <option value="Switzerland">Switzerland</option>
                        <option value="Syria">Syria</option>
                        <option value="Taiwan">Taiwan</option>
                        <option value="Tajikistan">Tajikistan</option>
                        <option value="Tanzania">Tanzania</option>
                        <option value="Thailand">Thailand</option>
                        <option value="Timor-Leste">Timor-Leste</option>
                        <option value="Togo">Togo</option>
                        <option value="Tonga">Tonga</option>
                        <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                        <option value="Tunisia">Tunisia</option>
                        <option value="Turkey">Turkey</option>
                        <option value="Turkmenistan">Turkmenistan</option>
                        <option value="Tuvalu">Tuvalu</option>
                        <option value="Uganda">Uganda</option>
                        <option value="Ukraine">Ukraine</option>
                        <option value="United Arab Emirates">United Arab Emirates</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="United States of America">United States of America</option>
                        <option value="Uruguay">Uruguay</option>
                        <option value="Uzbekistan">Uzbekistan</option>
                        <option value="Vanuatu">Vanuatu</option>
                        <option value="Vatican City">Vatican City</option>
                        <option value="Venezuela">Venezuela</option>
                        <option value="Vietnam">Vietnam</option>
                        <option value="Yemen">Yemen</option>
                        <option value="Zambia">Zambia</option>
                        <option value="Zimbabwe">Zimbabwe</option>
                    </select>
                </div>
                <button type="submit" name="add-member">Add Member</button>
            </form>
        </div>
        <?php
            echo "<br><br><br>";
            echo "<div class='table-container'>";
            echo "<table><tr><th class='narrowest-cell'>Index</th><th class='narrow-cell'>Name</th><th class='narrower-cell'>Number</th><th class='normal-cell'>Address</th><th class='delete-cell'></th></tr></table>";
            echo "</div>";
            echo "<div class='table-container'>";
            echo "<table>";
            $index = 0; // initialize index
            foreach ($_SESSION['addressBook'] as $index => $member) {
                // set a flag to indicate whether the button should show "Edit" or "Save"
                $editMode = isset($member['editMode']) ? $member['editMode'] : false; // set default value as false if not defined
                echo "<tr>";
                echo "<form class='btn-form' method='POST'>";
                echo "<td class='narrowest-cell'>{$index}</td>";
                if ($editMode) {
                    // input fields are not disabled during "Edit" mode
                    echo "<td class='narrow-cell'><input class='table-input' type='text' id='name-edit' name='name-edit' value='{$member['name']}'></td>";
                    echo "<td class='narrower-cell'><input class='table-input' type='text' id='number-edit' name='number-edit' value='{$member['number']}'></td>";
                    echo "<td class='normal-cell'><input class='table-input' type='text' id='address-edit' name='address-edit' value='{$member['address']}'></td>";
                } else {
                    // input fields are disabled during "Save" mode
                    echo "<td class='narrow-cell'><input class='table-input' type='text' id='name-edit' name='name-edit' value='{$member['name']}' disabled></td>";
                    echo "<td class='narrower-cell'><input class='table-input' type='text' id='number-edit' name='number-edit' value='{$member['number']}' disabled></td>";
                    echo "<td class='normal-cell'><input class='table-input' type='text' id='address-edit' name='address-edit' value='{$member['address']}' disabled></td>";
                }
                echo "<td class='action-cell'>
                      <div class='btn-wrapper'>
                      <input type='hidden' name='index' value='{$index}'>";
                // check if edit mode is on and change the button text accordingly
                if ($editMode) {
                    echo "<button class='edit-btn' name='save-member'>Save</button>";
                } else {
                    echo "<button class='edit-btn' name='edit-member'>Edit</button>";
                }
                echo "<button class='delete-btn' name='delete-member'>Delete</button>";
                echo "</div>
                      </td>
                      </form>";
                echo "</tr>";
                $index++;
            }            
            echo "</table>";
            echo "</div>";
        //session_destroy();
        //session_unset();
        ?>
    </div>
</body>
</html>