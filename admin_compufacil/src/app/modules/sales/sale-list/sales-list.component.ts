import { Component, OnInit, TemplateRef, ViewChild } from '@angular/core';
import { SalesService } from '../service/sales.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-sales-list',
  templateUrl: './sales-list.component.html',
  //   styleUrls: ['./sales-list.component.scss']
})
export class SalesListComponent implements OnInit {
  SALES: any = [];
  isLoading: any;
  search: any = null;
  selectedImage: string;
  saleDetails: any;

  @ViewChild('saleDetailsModal') saleDetailsModal: TemplateRef<any>;

  constructor(
    public salesService: SalesService,
    public modalService: NgbModal
  ) {}

  ngOnInit(): void {
    this.isLoading = this.salesService.isLoading$;
    this.listSales();
  }

  listSales() {
    this.salesService.listSales(this.search).subscribe((resp: any) => {
      console.log(resp);
      this.SALES = resp.sales;
    });
  }
  openImageModal(image: string, modal: TemplateRef<any>) {
    this.selectedImage = image;
    this.modalService.open(modal);
  }

  // viewSaleDetails(sale: any) {
  //   this.salesService.getSaleDetails(sale.id).subscribe((resp: any) => {
  //     console.log(resp);
  //     // Aquí puedes abrir un modal o navegar a una nueva página para mostrar los detalles
  //   });
  // }
  viewSaleDetails(sale: any) {
    this.salesService.getSaleDetails(sale.id).subscribe((resp: any) => {
      console.log(resp);
      this.saleDetails = resp.sale; // Almacena los detalles de la venta
      this.modalService.open(this.saleDetailsModal); // Abre el modal
    });
  }

  updateCourseStudentState(courseStudentId: number, state: number) {
    this.salesService
      .updateCourseStudentState(courseStudentId, state)
      .subscribe((resp: any) => {
        console.log(resp);
        // Aquí puedes actualizar la lista de ventas o el estado del curso en la interfaz
      });
  }

  toggleCourseState(detail: any) {
    const newState = detail.course_student_state === 1 ? 0 : 1; // Cambia el estado
    detail.course_student_state = newState; // Actualiza el estado en tiempo real

    this.salesService
      .updateCourseStudentState(detail.course_student_id, newState)
      .subscribe(
        (resp: any) => {
          console.log(resp);
          // Aquí puedes mostrar un mensaje de éxito si lo deseas
        },
        (error) => {
          console.error('Error al actualizar el estado del curso', error);
          // Si deseas, puedes revertir el cambio en caso de error
          detail.course_student_state = newState === 1 ? 0 : 1; // Revertir el estado
        }
      );
  }
}
