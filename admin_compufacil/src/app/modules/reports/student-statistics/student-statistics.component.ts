import { Component, OnInit } from '@angular/core';
import { ReportService } from '../service/report.service';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-student-statistics',
  templateUrl: './student-statistics.component.html',
  //   styleUrls: ['./student-statistics.component.scss']
})
export class StudentStatisticsComponent implements OnInit {
  statistics: any[] = [];
  filters = {
    startDate: '',
    endDate: '',
  };
  isLoading$: Observable<boolean>;

  constructor(private reportService: ReportService) {
    this.isLoading$ = this.reportService.isLoading$;
  }

  ngOnInit(): void {
    this.getStatistics();
  }

  getStatistics(): void {
    this.reportService.getStudentStatistics(this.filters).subscribe(
      (response: any) => {
        this.statistics = response.statistics;
      },
      (error) => {
        console.error('Error fetching student statistics:', error);
      }
    );
  }

  downloadReport(format: string): void {
    this.reportService
      .downloadStudentStatistics(this.filters, format)
      .subscribe(
        (blob: Blob) => {
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `student_statistics.${format}`;
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          window.URL.revokeObjectURL(url);
        },
        (error) => {
          console.error(`Error downloading ${format} report:`, error);
        }
      );
  }
}
