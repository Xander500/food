<!-- imports -->
<script src="https://nosir.github.io/cleave.js/dist/cleave.min.js"></script>
<script src="https://nosir.github.io/cleave.js/dist/cleave-phone.i18n.js"></script>

<main>
    <div class="main-content-box">
        <form class="add-org-form" method="post">
	        <div class="text-center">
                <h2 class="mb-8">Add Organization Form</h2>
                <div class="info-box">
                    <p>Add a record of a non-profit or community organization with whom students may volunteer.
                        <br>An asterisk (*) indicates a required field.</p>
                </div>
	        </div>
        
            <fieldset class="section-box mb-4">
                <label for="name">* Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter the organization's name">

                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Enter a description of the organization" rows="3"></textarea>
            
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="Enter the location of the organization">

                <label for="email">* E-mail</label>
                <input type="email" id="email" name="email" required placeholder="Enter the e-mail address of the organization">
            </fieldset>
            <input type="submit" name="registration-form" value="Submit" style="width: 35%; margin: auto;">
        </form>
   </div> 
</main>
