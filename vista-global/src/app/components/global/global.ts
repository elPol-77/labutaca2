import { Component, OnInit, HostListener } from '@angular/core';
import { TmdbService } from '../../services/tmdb';
import { TmdbMovie } from '../../common/tmdb';
import { forkJoin } from 'rxjs';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-global',
  templateUrl: './global.html',
  styleUrls: ['./global.css'],
  imports: [CommonModule]
})

export class GlobalComponent implements OnInit {

  movies: TmdbMovie[] = [];
  loading: boolean = false;
  
  // Variables para controlar el infinito
  currentPage: number = 1;

  constructor(private tmdbService: TmdbService) { }

  ngOnInit(): void {
    this.cargarMasPeliculas();
  }

  // --- DETECTOR DE SCROLL ---
  @HostListener('window:scroll', [])
  onScroll(): void {
    // Altura total de la ventana + lo que has scrolleado
    const pos = (document.documentElement.scrollTop || document.body.scrollTop) + document.documentElement.offsetHeight;
    // Altura total del documento
    const max = document.documentElement.scrollHeight;

    // Si llegas cerca del final (a 500px) y no está cargando ya...
    if (pos > max - 500 && !this.loading) {
      this.cargarMasPeliculas();
    }
  }

  cargarMasPeliculas() {
    this.loading = true;

    // Pedimos la página actual (empieza en 1, luego 2, 3...)
    this.tmdbService.getDiscoveryMovies(this.currentPage).subscribe(newMovies => {
      
      // Añadimos las nuevas al final de la lista existente
      this.movies.push(...newMovies);
      
      // Preparamos la siguiente página
      this.currentPage++;
      
      this.loading = false;
    });
  }

  getImage(path: string | null): string {
    return path ? `https://image.tmdb.org/t/p/w500${path}` : 'assets/img/no-poster.jpg';
  }

  goToDetail(movie: TmdbMovie) {
    // Redirige a la vista de detalle de CodeIgniter
    window.location.href = `/detalle/${movie.id}`;
  }
}
