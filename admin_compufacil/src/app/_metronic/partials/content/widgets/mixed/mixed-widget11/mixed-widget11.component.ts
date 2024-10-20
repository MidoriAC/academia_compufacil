import {
  Component,
  Input,
  OnInit,
  OnDestroy,
  ChangeDetectorRef,
  ViewChild,
} from '@angular/core';
import { getCSSVariableValue } from '../../../../../kt/_utils';
import { DashboardService } from '../../dashboard.service';
import { Subscription } from 'rxjs';
import { ApexChart, ChartComponent } from 'ng-apexcharts';

interface MonthlySale {
  x: string;
  y: number;
}

const DEFAULT_CHART_OPTIONS: ApexChart = {
  type: 'bar',
  height: 350,
  animations: {
    enabled: false,
  },
};

@Component({
  selector: 'app-mixed-widget11',
  templateUrl: './mixed-widget11.component.html',
})
export class MixedWidget11Component implements OnInit, OnDestroy {
  @ViewChild('chart') chart: ChartComponent;
  @Input() chartColor: string = '';
  @Input() chartHeight: string = '350px';

  public chartOptions: Partial<ChartComponent> = {};
  totalRevenue: number = 0;
  dateRange: string = '';
  private subscription: Subscription;
  defaultChartOptions: ApexChart = DEFAULT_CHART_OPTIONS;

  constructor(
    private dashboardService: DashboardService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.loadChartData();
  }

  ngOnDestroy(): void {
    if (this.subscription) {
      this.subscription.unsubscribe();
    }
  }

  private loadChartData(): void {
    this.subscription = this.dashboardService.getDashboardStats().subscribe(
      (response: any) => {
        console.log('Response:', response);
        this.totalRevenue = response.total_revenue;
        const monthlySales: MonthlySale[] = response.monthly_sales;

        if (monthlySales && monthlySales.length > 0) {
          const months = monthlySales.map((item: MonthlySale) => item.x);
          this.dateRange = `${months[0]} - ${
            months[months.length - 1]
          } ${new Date().getFullYear()}`;

          this.chartOptions = this.getChartOptions(monthlySales);
        } else {
          console.error('No se recibieron datos de ventas mensuales');
          this.chartOptions = this.getChartOptions([]);
        }
        this.cdr.detectChanges();
      },
      (error) => {
        console.error('Error al obtener los datos del dashboard:', error);
        this.chartOptions = this.getChartOptions([]);
        this.cdr.detectChanges();
      }
    );
  }

  private getChartOptions(
    monthlySales: MonthlySale[]
  ): Partial<ChartComponent> {
    const labelColor = getCSSVariableValue('--bs-gray-500');
    const borderColor = getCSSVariableValue('--bs-gray-200');
    const baseColor = getCSSVariableValue('--bs-' + this.chartColor);

    return {
      series: [
        {
          name: 'Ventas',
          data: monthlySales.map((item: MonthlySale) => item.y),
        },
      ],
      chart: {
        ...DEFAULT_CHART_OPTIONS,
        fontFamily: 'inherit',
        type: 'bar',
        height: this.chartHeight,
        toolbar: {
          show: false,
        },
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '50%',
          borderRadius: 5,
        },
      },
      legend: {
        show: false,
      },
      dataLabels: {
        enabled: false,
      },
      stroke: {
        show: true,
        width: 2,
        colors: ['transparent'],
      },
      xaxis: {
        categories: monthlySales.map((item: MonthlySale) => item.x),
        axisBorder: {
          show: false,
        },
        axisTicks: {
          show: false,
        },
        labels: {
          style: {
            colors: labelColor,
            fontSize: '12px',
          },
        },
      },
      yaxis: {
        labels: {
          style: {
            colors: labelColor,
            fontSize: '12px',
          },
        },
      },
      fill: {
        type: 'solid',
      },
      states: {
        normal: {
          filter: {
            type: 'none',
            value: 0,
          },
        },
        hover: {
          filter: {
            type: 'none',
            value: 0,
          },
        },
        active: {
          allowMultipleDataPointsSelection: false,
          filter: {
            type: 'none',
            value: 0,
          },
        },
      },
      tooltip: {
        style: {
          fontSize: '12px',
        },
        y: {
          formatter: function (val: number) {
            return 'Q' + val.toFixed(2) + ' ingresos';
          },
        },
      },
      colors: [baseColor],
      grid: {
        padding: {
          top: 10,
        },
        borderColor: borderColor,
        strokeDashArray: 4,
        yaxis: {
          lines: {
            show: true,
          },
        },
      },
    };
  }
}
