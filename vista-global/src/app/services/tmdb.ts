import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { TmdbResponse, TmdbMovie } from '../common/tmdb';

@Injectable({
  providedIn: 'root'
})
export class TmdbService {
  private apiKey = '6387e3c183c454304108333c56530988'; // <--- PEGA TU API KEY DE TMDB AQUÍ
  private baseUrl = 'https://api.themoviedb.org/3';

  constructor(private http: HttpClient) { }

  // Utilidad privada para hacer la petición
  private getQuery(query: string): Observable<TmdbMovie[]> {
    const url = `${this.baseUrl}${query}&api_key=${this.apiKey}&language=es-ES`;
    return this.http.get<TmdbResponse>(url).pipe(
      map(response => response.results)
    );
  }

  // 1. Tendencias (Mix Pelis y Series)
  getTrending(): Observable<TmdbMovie[]> {
    return this.getQuery('/trending/all/week?');
  }

  // 2. Películas Populares
  getPopularMovies(): Observable<TmdbMovie[]> {
    return this.getQuery('/movie/popular?');
  }

  // 3. Series Populares
  getPopularSeries(): Observable<TmdbMovie[]> {
    return this.getQuery('/tv/popular?');
  }

  // 4. Por Género (Acción: 28, Comedia: 35, Terror: 27, Sci-Fi: 878)
  getByGenre(genreId: number): Observable<TmdbMovie[]> {
    return this.getQuery(`/discover/movie?with_genres=${genreId}`);
  }

  // 5. Buscador Global
  search(texto: string): Observable<TmdbMovie[]> {
    return this.getQuery(`/search/multi?query=${texto}`);
  }
  getGenreList(): Observable<any> {
    const url = `${this.baseUrl}/genre/movie/list?api_key=${this.apiKey}&language=es-ES`;
    return this.http.get<any>(url).pipe(map(res => res.genres));
  }

  // 2. Películas mejor valoradas (Crítica)
  getTopRated(): Observable<TmdbMovie[]> {
    return this.getQuery('/movie/top_rated?');
  }

  // 3. Próximos estrenos
  getUpcoming(): Observable<TmdbMovie[]> {
    return this.getQuery('/movie/upcoming?');
  }

  // === NUEVO MÉTODO PARA SCROLL INFINITO ===
  getDiscoveryMovies(page: number): Observable<TmdbMovie[]> {
    // Usamos /discover/movie para poder paginar y ordenar por popularidad
    const url = `${this.baseUrl}/discover/movie?api_key=${this.apiKey}&language=es-ES&sort_by=popularity.desc&include_adult=false&include_video=false&page=${page}`;
    
    return this.http.get<TmdbResponse>(url).pipe(
      map(response => response.results)
    );
  }
}