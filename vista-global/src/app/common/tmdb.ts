export interface TmdbResponse {
  page: number;
  results: TmdbMovie[];
  total_pages: number;
  total_results: number;
}

export interface TmdbMovie {
  id: number;
  title?: string;        // En pelis se llama title
  name?: string;         // En series se llama name
  poster_path: string | null;
  backdrop_path: string | null;
  overview: string;
  release_date?: string;
  first_air_date?: string;
  vote_average: number;
  genre_ids: number[];
  media_type?: string;   // 'movie' o 'tv'
  loaded?: boolean;
}