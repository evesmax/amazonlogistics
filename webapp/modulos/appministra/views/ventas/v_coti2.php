<div id="page-wrap">

        <textarea id="header">INVOICE</textarea>
        
        <div id="identity">
        
            <textarea id="address">Chris Coyier
123 Appleseed Street
Appleville, WI 53719

Phone: (555) 555-5555</textarea>

            <div id="logo">

              <div id="logoctr">
                <a title="Change logo" id="change-logo" href="javascript:;">Change Logo</a>
                <a title="Save changes" id="save-logo" href="javascript:;">Save</a>
                |
                <a title="Delete logo" id="delete-logo" href="javascript:;">Delete Logo</a>
                <a title="Cancel changes" id="cancel-logo" href="javascript:;">Cancel</a>
              </div>

              <div id="logohelp">
                <input type="text" value="" size="50" id="imageloc"><br>
                (max width: 540px, max height: 100px)
              </div>
              <img alt="logo" src="images/logo.png" id="image">
            </div>
        
        </div>
        
        <div style="clear:both"></div>
        
        <div id="customer">

            <textarea id="customer-title">Widget Corp.
c/o Steve Widget</textarea>

            <table id="meta">
                <tbody><tr>
                    <td class="meta-head">Invoice #</td>
                    <td><textarea>000123</textarea></td>
                </tr>
                <tr>

                    <td class="meta-head">Date</td>
                    <td><textarea id="date">December 15, 2009</textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Amount Due</td>
                    <td><div class="due">$875.00</div></td>
                </tr>

            </tbody></table>
        
        </div>
        
        <table id="items">
        
          <tbody><tr>
              <th>Item</th>
              <th>Description</th>
              <th>Unit Cost</th>
              <th>Quantity</th>
              <th>Price</th>
          </tr>
          
          <tr class="item-row">
              <td class="item-name"><div class="delete-wpr"><textarea>Web Updates</textarea><a title="Remove row" href="javascript:;" class="delete">X</a></div></td>
              <td class="description"><textarea>Monthly web updates for http://widgetcorp.com (Nov. 1 - Nov. 30, 2009)</textarea></td>
              <td><textarea class="cost">$650.00</textarea></td>
              <td><textarea class="qty">1</textarea></td>
              <td><span class="price">$650.00</span></td>
          </tr>
          
          <tr class="item-row">
              <td class="item-name"><div class="delete-wpr"><textarea>SSL Renewals</textarea><a title="Remove row" href="javascript:;" class="delete">X</a></div></td>

              <td class="description"><textarea>Yearly renewals of SSL certificates on main domain and several subdomains</textarea></td>
              <td><textarea class="cost">$75.00</textarea></td>
              <td><textarea class="qty">3</textarea></td>
              <td><span class="price">$225.00</span></td>
          </tr>
          
          <tr id="hiderow">
            <td colspan="5"><a title="Add a row" href="javascript:;" id="addrow">Add a row</a></td>
          </tr>
          
          <tr>
              <td class="blank" colspan="2"> </td>
              <td class="total-line" colspan="2">Subtotal</td>
              <td class="total-value"><div id="subtotal">$875.00</div></td>
          </tr>
          <tr>

              <td class="blank" colspan="2"> </td>
              <td class="total-line" colspan="2">Total</td>
              <td class="total-value"><div id="total">$875.00</div></td>
          </tr>
          <tr>
              <td class="blank" colspan="2"> </td>
              <td class="total-line" colspan="2">Amount Paid</td>

              <td class="total-value"><textarea id="paid">$0.00</textarea></td>
          </tr>
          <tr>
              <td class="blank" colspan="2"> </td>
              <td class="total-line balance" colspan="2">Balance Due</td>
              <td class="total-value balance"><div class="due">$875.00</div></td>
          </tr>
        
        </tbody></table>
        
        <div id="terms">
          <h5>Terms</h5>
          <textarea>NET 30 Days. Finance Charge of 1.5% will be made on unpaid balances after 30 days.</textarea>
        </div>
    
    </div>