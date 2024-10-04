import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { CartService } from '../../tienda-guest/service/cart.service';

declare function alertSuccess([]): any;
declare function alertDanger([]): any;
declare var paypal: any;
@Component({
  selector: 'app-list-carts',
  templateUrl: './list-carts.component.html',
  styleUrls: ['./list-carts.component.css'],
})
export class ListCartsComponent implements OnInit {
  listCarts: any = [];
  totalSum: number = 0;
  code: any = null;
  selectedFile: File | null = null;
  @ViewChild('paypal', { static: true }) paypalElement?: ElementRef;
  constructor(public cartService: CartService) {}
  ngOnInit(): void {
    this.cartService.currentData$.subscribe((resp: any) => {
      console.log(resp);
      this.listCarts = resp;
      this.totalSum = this.listCarts.reduce(
        (sum: number, item: any) => sum + item.total,
        0
      );
    });
  }

  getNameCampaing(type: number) {
    let Name = '';
    switch (type) {
      case 1:
        Name = 'CAMPAÑA NORMAL';
        break;
      case 2:
        Name = 'CAMPAÑA FLASH';
        break;
      case 3:
        Name = 'CAMPAÑA BANNER';
        break;

      default:
        break;
    }

    return Name;
  }

  removeItem(cart: any) {
    this.cartService.deleteCart(cart.id).subscribe((resp: any) => {
      console.log(resp);
      alertSuccess('EL ITEM SE A ELIMINADO CORRECTAMENTE ');
      this.cartService.removeItemCart(cart);
    });
  }

  applyCupon() {
    if (!this.code) {
      alertDanger('NECESITAS INGRESAR UN CUPON');
      return;
    }
    let data = {
      code: this.code,
    };
    this.cartService.applyCupon(data).subscribe((resp: any) => {
      console.log(resp);
      if (resp.message == 403) {
        alertDanger(resp.message_text);
      } else {
        this.cartService.resetCart();
        setTimeout(() => {
          resp.carts.data.forEach((cart: any) => {
            this.cartService.addCart(cart);
          });
        }, 50);
        alertSuccess('EL CUPON SE HA REGISTRADO CORRECTAMENTE');
      }
    });
  }

  onFileSelected(event: any): void {
    this.selectedFile = event.target.files[0];
  }

  checkout(): void {
    if (!this.selectedFile) {
      alertDanger('Por favor, seleccione un comprobante de pago.');
      return;
    }

    const formData = new FormData();
    formData.append('comprobante', this.selectedFile);
    formData.append('method_payment', '1'); // 1 for deposit
    formData.append('total', this.totalSum.toString());

    this.cartService.checkoutWithComprobante(formData).subscribe(
      (response: any) => {
        console.log(response);
        alertSuccess(
          'Compra realizada con éxito. El comprobante ha sido enviado.'
        );
        this.cartService.resetCart();
      },
      (error) => {
        console.error(error);
        alertDanger(
          'Error al procesar la compra. Por favor, intente nuevamente.'
        );
      }
    );
  }
}
