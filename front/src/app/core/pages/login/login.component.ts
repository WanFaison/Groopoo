import { Component, OnInit } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, CommonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnInit{
  username: string = '';
  password: string = '';
  errorMessage: any

  constructor(private authService:AuthServiceImpl, private router:Router){
  }

  ngOnInit(): void {
  }

  onLogin(): void {
    this.authService.login(this.username, this.password).subscribe(data=>{
      if(data.status==200){
        this.authService.setAuth(true)
        this.router.navigateByUrl("/app/home")
      }else{
         this.errorMessage=data.results
      }
    });
  }

}
