<script>
  function myFunction()
{
  window.print();
}
</script>
<style>
  @media print
{
.print-image
{
  display: none;
}
}
</style>
<div>
 <a href="javascript:void(0);" class="print-image" onclick="myFunction();"><img style="width: 35px; height: 50px; margin-right: 20px; float: right; " src="/providers/logos/print.webp"></a>

  <main id="printInvoice" style="display: flex; flex-direction: column;">

    <!-- Header -->
    <section style="margin-inline: 20px; display: flex; justify-content: space-between; font-size: 14px;">
      <div style="display: flex;">
          <img style="width: 35px; height: 50px; margin-right: 20px;" src="/providers/logos/15989633435f4e3e8f422cc-logo---Qreeb-1.png">
          <div style="display: flex; flex-direction: column;">
              <div style="font-weight: bold;">{{  $provider->en_organization_name }}.</div>
              <div>PO Box: {{  $provider->po_box }}</div>
              <div>{{  $provider->address->parent->en_name }} - {{  $provider->address->en_name  }}</div>
              <div>Telephone: {{  unserialize($provider->phones)['0'] }} E-Mail: {{  $provider->email }}</div>
          </div>
      </div>
      <div>
          <div style="display: flex; justify-content: center; margin-bottom: 20px;">
              <div style="margin-right: 20px;">
                <img src="{{  asset('storage/qrcode/'.$data['img_qr_code']) }}" width="50" height="50">
              </div>

          </div>
      </div>
          <div style="display: flex; flex-direction: column; text-align: right;">
              <div style="font-weight: bold;">{{  $provider->ar_organization_name }}</div>
              <div> <span>{{  $provider->po_box }} :ص ب</span></div>
              <div>{{  $provider->address->parent->ar_name }} - {{  $provider->address->ar_name }}</div>
              <div> {{  unserialize($provider->phones)['0'] }} :رقم الهاتف </div>
               <div> {{  $provider->email }} :البريد الإلكتروني</div>

          </div>
      </section>

    <!-- Line -->
    <section style="margin-inline: 20px;">
        <div style="border-bottom: 2px solid #668bcf; margin-top: 5px;"></div>
    </section>

    <!-- PO Info -->
    <section style="margin-inline: 20px; display: flex; flex-direction: column;">
        <!-- Heading -->
        <div style="display: flex; justify-content: center; margin-bottom: 20px;">
            <div style="font-size: 28px; font-weight: bold; color: #5b9bd5; margin-right: 20px;">Invoice</div>
            <div style="font-size: 28px; font-weight: bold; color: #5b9bd5; margin-left: 20px;"> فاتورة</div>
        </div>

        <!-- Details -->
        <div style="display: flex; justify-content: space-between; font-size: 12px;">
            <div style="display: flex;">
              <div style="display: flex; flex-direction: column; font-weight: bold; line-height: 20px; font-size: 12px;">
                  <div>Invoice No: </div>
                  <div>Invoice Date: </div>
              </div>
              <div style="display: flex; flex-direction: column; text-align: left; margin-left: 10px; line-height: 20px; font-size: 12px;">
                <div>
                  {{ $data['id'] }}
                </div>
                <div>
                  {{  \Carbon\Carbon::parse($data['date'])->format('M-Y') }}
                </div>
              </div>
            </div>

            <div style="display: flex;">
              <div style="display: flex; flex-direction: column; text-align: right; margin-right: 10px; line-height: 20px; font-size: 12px;">
                <div>
                  {{ $data['id'] }}
                </div>
                <div>
                  {{  \Carbon\Carbon::parse($data['date'])->format('M-Y') }}
                </div>
              </div>
              <div style="display: flex; flex-direction: column; text-align: right; font-weight: bold; line-height: 20px; font-size: 12px;">
                <div>  :رقم أمر الشراء</div>
                <div>  :تاريخ أمر الشراء</div>
              </div>
            </div>
        </div>
    </section>

    <!-- Seller Buyer  -->

    <section
    style="margin-inline: 20px; display: flex; justify-content: space-between; margin-top: 20px; font-size: 12px;">
    <div style="border: 1px solid #5b9bd5; width: 48%; height: 200px; border-radius: 50px;
    margin-left: 10px; background-color: #f2f2f2;">
      <div style="line-height:18px; display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 25px; font-weight: bold; color: #6899c7">
        <div>Provider</div>
        <div>مقدم الخدمة</div>
      </div>
      <div style="line-height:18px; display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;color: #5b9bd5;">
        <div>  {{  Auth::guard('provider')->user()->username }} </div>
        <div>{{  $provider->ar_name }}</div>
      </div>
      <div style="line-height:18px; display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;">
        <div>City: {{  $provider->address->en_name }}</div>
        <div>{{  $provider->address->ar_name }}</div>
      </div>
      {{-- <div style=" line-height:18px; display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;">
        <div>Contact Person: {{printData?.vendorContactPerson}}</div>
        <div>بيانات الشخص</div>
      </div> --}}
      <div style="line-height:18px; display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;">
        <div>Contact No: {{ unserialize($provider->phones)['0'] }}</div>
        <div>رقم التواصل</div>
      </div>
      <div style="line-height:18px; display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;">
        <div>Email: {{$provider->email}}</div>
        <div>البريد الاكتروني</div>
      </div>
      <div style="line-height:18px; display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;">
        <div><span style=" color: #6899c7;">VAT Ref#:</span> {{ $data['vat_registration']  }}</div>
        <div style=" color: #6899c7;">:رقم ضربية القيمة المضافة</div>
      </div>
    </div>


    <div style="border: 1px solid #5b9bd5; width: 48%; height: 200px; border-radius: 50px;margin-left: 10px; background-color: #f2f2f2;">
      <div style="line-height:18px; display:flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 25px; font-weight: bold; color: #6899c7">
        <div>Company</div>
        <div>شركة</div>
      </div>
      <div style="line-height:18px; display:flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;color: #5b9bd5;">
        <div>{{  $company->en_name }}</div>
        <div>{{  $company->ar_name }}</div>
      </div>
      <div style="display:flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;">
        <div>City: {{  $company->address->en_name }}
          </div>
        <div> {{  $company->address->ar_name }}</div>
      </div>
      {{-- <div style="line-height:18px; display:flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;">
        <div>Contact Person:  NIZAM KUDRATAY
        </div>
        <div>بيانات الشخص</div>
      </div> --}}

      <div style="line-height:18px; line-height:18px; display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;">
        <div>Contact No:
           {{  unserialize($company->phones)['0'] }}
          </div>
        <div>رقم التواصل</div>
      </div>
      <div style="line-height:18px; display:flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;">
        <div>Email:
           {{  $company->email }}
        </div>
        <div>البريد الاكتروني</div>
      </div>
      <div style="line-height:18px; display:flex; flex-direction: row; justify-content: space-between; align-items: center; padding-left: 15px; padding-right: 15px; margin-top: 5px;">
        <div><span style=" color: #6899c7;">VAT Ref#:</span>
          {{ $data['vat_registration']  }}
        </div>
        <div style=" color: #6899c7;">:رقم ضربية القيمة المضافة</div>
      </div>
    </div>
  </section>

    <!-- Table -->
    <section style="margin-inline: 20px;">
        <table style="width: 100%; margin-top: 20px; border: 1px solid #5a5959; border-collapse: collapse;
        text-align: center;">
            <tr style="background-color: #5b9bd5; height: 70px; width: 100%; font-size: 10px;">

                <th style="border: 1px solid #5a5959; color: white;">Order Id<br>
                  <span>معلومات أداء العمل</span></th>
                  <th style="border: 1px solid #5a5959; color: white;">Date<br>
                    <span>عدد التقييم</span></th>
                    <th style="border: 1px solid #5a5959; color: white;">Service Description<br>
                        <span>الخدمات
                          </span></th>
                <th style="border: 1px solid #5a5959; color: white;">QTY<br>
                  <span>الكمية
                    </span></th>
                <th style="border: 1px solid #5a5959; color: white;">Rate<br>
                  <span>عدد التقييم</span></th>

                <th style="border: 1px solid #5a5959; color: white;">Total Amount<br>
                    <span>عدد التقييم</span></th>


            </tr>

            <style>
            /* tr.myContainer:nth-child(even) {
              background-color: #dddddd;
            } */
            tr
            {
              background-color: #ffffff;
            }
            </style>

            {{-- <ng-container  *ngFor="let row of printData.pmsgrnInvoiceDetails; let i = index"> --}}

              @foreach ($data['invoiceMaterial'] as $order)

            <tr>
              <td  style="border: 1px solid #5a5959;">{{  !empty($order['order_id'])? $order['order_id']: '' }}</td>
              <td style="border: 1px solid #5a5959;">{{ !empty($order['created_at'])? \Carbon\Carbon::parse($order['created_at'])->format('d-M-Y h:i:s') : '' }}</td>
              <td style="border: 1px solid #5a5959;">{{  !empty($order['service_name'])? $order['service_name'] : '' }}</td>
              <td style="border: 1px solid #5a5959;">{{  !empty($order['qty'])? $order['qty'] : 0 }}</td>
              <td style="border: 1px solid #5a5959;">{{ !empty($order['price'])? $order['price'] : 0 }}</td>
              <td style="border: 1px solid #5a5959;">{{ !empty($order['item_amount'])? $order['item_amount'] : 0 }}</td>
            </tr>
            @endforeach
            {{-- </ng-container> --}}

              <tr style="font-size: 10px;">
                  <td colspan="2" style="border: 1px solid #5a5959; border-bottom-color: transparent;" ></td>
                  <td colspan="2" style="border: 1px solid #5a5959; text-align: left; padding-left: 2px;" >
                      Subtotal
                  </td>
                  <td dir="rtl" style="border: 1px solid #5a5959; text-align: right; padding-right: 2px;" >
                      المجموع الفرعي
                  </td>
                  <td style="border: 1px solid #5a5959;text-align: right; padding-right: 2px;" >
                      {{ !empty($data['item_amount_sum_total'])? $data['item_amount_sum_total']: '' }} SAR
                  </td>
              </tr>

              <tr style="font-size: 10px;">
                  <td colspan="2" style="border: 1px solid #5a5959; border-bottom-color: transparent;" ></td>
                  <td colspan="2" style="border: 1px solid #5a5959; text-align: left; padding-left: 2px;" >
                    VAT
                  </td>
                  <td style="border: 1px solid #5a5959; text-align: right; padding-right: 2px;">ضريبة القيمة المضافة
                  </td>
                  <td style="border: 1px solid #5a5959;text-align: right; padding-right: 2px;">
                      {{ !empty($data['vat'])? $data['vat'] : '' }} %
                  </td>
              </tr>

              <tr style="font-size: 10px;">
                  <td colspan="2" style="border: 1px solid #5a5959; border-bottom-color: transparent;" ></td>
                  <td colspan="2" style="border: 1px solid #5a5959; text-align: left; padding-left: 2px;" >
                    Total Vat Amount
                  </td>
                  <td style="border: 1px solid #5a5959; text-align: right; padding-right: 2px;" >إجمالي العمولة المستحقة
                  </td>
                  <td style="border: 1px solid #5a5959;text-align: right; padding-right: 2px;" >
                    {{ !empty($data['material_vat_total'])? $data['material_vat_total'] : '' }} SAR
                  </td>
              </tr>
              <tr style="font-size: 10px;">
                <td colspan="2" style="border: 1px solid #5a5959;" ></td>
                <td colspan="2" style="border: 1px solid #5a5959; text-align: left; padding-left: 2px;" >
                    Total Amount
                </td>
                <td style="border: 1px solid #5a5959; text-align: right; padding-right: 2px;" >إجمالي                 </td>
                <td style="border: 1px solid #5a5959;text-align: right; padding-right: 2px;" >
                  {{  $data['material_vat_total'] + $data['item_amount_sum_total'] }} SAR
                </td>
            </tr>
        </table>
        <div style="margin-top: 20%; align-items: center;
        justify-content: center;
        display: flex;">
          <div style="border: 2px solid black;
          width: 80%; padding: 4px; font-size: 13px;">
            By Accepting this P.O. you agree to fully comply with the policies as set out in the safsdfsdfs rules. Your attention is drawn
              particularly to the anti-corruption bribery policies. Copy of the policies available upon request
          </div>
        </div>

    </section>

  </main>
</div>
