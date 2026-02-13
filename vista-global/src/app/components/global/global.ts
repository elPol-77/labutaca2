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
    const pos = (document.documentElement.scrollTop || document.body.scrollTop) + document.documentElement.offsetHeight;
    const max = document.documentElement.scrollHeight;

    if (pos > max - 500 && !this.loading) {
      this.cargarMasPeliculas();
    }
  }

  cargarMasPeliculas() {
    this.loading = true;

    this.tmdbService.getDiscoveryMovies(this.currentPage).subscribe(newMovies => {
      
      this.movies.push(...newMovies);
      
      this.currentPage++;
      
      this.loading = false;
    });
  }

  getImage(path: string | null): string {
    return path ? `https://image.tmdb.org/t/p/w500${path}` : 'assets/img/no-poster.jpg';
  }

  goToDetail(movie: TmdbMovie) {
    window.location.href = `/labutaca2/detalle/tmdb_movie_${movie.id}`;
  }
}
